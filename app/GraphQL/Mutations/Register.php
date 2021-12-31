<?php

namespace App\GraphQL\Mutations;


use DanielDeWit\LighthouseSanctum\Contracts\Services\EmailVerificationServiceInterface;
use DanielDeWit\LighthouseSanctum\Exceptions\HasApiTokensException;
use DanielDeWit\LighthouseSanctum\Traits\CreatesUserProvider;
use Exception;
use Illuminate\Auth\AuthManager;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Laravel\Sanctum\Contracts\HasApiTokens;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;


class Register
{
    use CreatesUserProvider;

    protected AuthManager $authManager;
    protected Config $config;
    protected Hasher $hash;
    protected EmailVerificationServiceInterface $emailVerificationService;

    public function __construct(
        AuthManager $authManager,
        Config $config,
        Hasher $hash,
        EmailVerificationServiceInterface $emailVerificationService
    ) {
        $this->authManager              = $authManager;
        $this->config                   = $config;
        $this->hash                     = $hash;
        $this->emailVerificationService = $emailVerificationService;
    }

    /**
     * @param mixed $_
     * @param array<string, mixed> $args
     * @return array<string, string|null>
     * @throws Exception
     */
    public function __invoke($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        /** @var EloquentUserProvider $userProvider */
        $userProvider = $this->createUserProvider();


        $ClientIP   = $context->request()->getClientIp();
        $Timezone   =  geoip($ClientIP)->timezone;

        $args["timezone"] = $Timezone;

        $user = $this->saveUser(
            $userProvider->createModel(),
            $this->getPropertiesFromArgs($args),
        );

        if ($user instanceof MustVerifyEmail) {
            if (isset($args['verification_url'])) {
                $this->emailVerificationService->setVerificationUrl($args['verification_url']['url']);
            }

            $user->sendEmailVerificationNotification();

            return [
                'token'  => null,
                'status' => 'MUST_VERIFY_EMAIL',
            ];
        }

        if (!$user instanceof HasApiTokens) {
            throw new HasApiTokensException($user);
        }


        \Mail::to($args["email"])->send(new \App\Mail\SignupEmail(["name" => $args["name"]]));

        //  Notify the Admin About the New Signup
        $ToEmail = env('MAIL_OWNER_EMAIL', 'muhaddisshah@gmail.com');
        \Mail::to($ToEmail)->send(new \App\Mail\NewSignupNotification(["name" => $args["name"], "email" => $args["email"]]));

        return [
            'token'  => $user->createToken('default')->plainTextToken,
            'status' => 'SUCCESS',
        ];
    }

    /**
     * @param Model $user
     * @param array<string, mixed> $attributes
     * @return Model
     */
    protected function saveUser(Model $user, array $attributes): Model
    {
        $user
            ->fill($attributes)
            ->save();

        return $user;
    }

    /**
     * @param array<string, mixed> $args
     * @return array<string, string>
     */
    protected function getPropertiesFromArgs(array $args): array
    {
        $properties = Arr::except($args, [
            'directive',
            'password_confirmation',
            'verification_url',
        ]);

        $properties['password'] = $this->hash->make($properties['password']);

        return $properties;
    }

    protected function getAuthManager(): AuthManager
    {
        return $this->authManager;
    }

    protected function getConfig(): Config
    {
        return $this->config;
    }
}
