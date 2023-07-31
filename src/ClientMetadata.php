<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect;

use Srako\OpenIDConnect\Authentication\AuthenticationMethod;
use Srako\OpenIDConnect\Authentication\ClientSecretPost;
use Srako\OpenIDConnect\Exception\MissingParamException;

final class ClientMetadata
{
    public const CLIENT_ID = 'client_id';
    public const CLIENT_SECRET = 'client_secret';
    public const REDIRECT_URI = 'redirect_uri';

    private string $id;
    private ?string $secret;
    private ?string $redirectUri;
    private ?AuthenticationMethod $authenticationMethod;

    public function __construct(
        string                $id,
        ?string               $secret = null,
        ?string               $redirectUri = null,
        ?AuthenticationMethod $authenticationMethod = null
    )
    {
        $this->id = $id;
        $this->secret = $secret;
        $this->redirectUri = $redirectUri;
        $this->authenticationMethod = $authenticationMethod ?? new ClientSecretPost();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function secret(): string
    {
        if (!$this->secret) {
            throw new MissingParamException('client_secret');
        }
        return $this->secret;
    }

    public function redirectUri(): ?string
    {
        return $this->redirectUri;
    }

    public function authenticationMethod(): ?AuthenticationMethod
    {
        return $this->authenticationMethod;
    }
}
