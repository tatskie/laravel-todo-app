<?php

namespace App\Models;

use App\User;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use League\OAuth2\Server\CryptKey;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\Bridge\AccessToken as PassportAccessToken;

class AccessToken extends Model
{
    use HasFactory;

    private $privateKey;

    /**
     * Convertion to JWT
     *
     * @var CryptKey $privateKey
     */
    public function convertToJWT(CryptKey $privateKey)
    {
        $builder = new Builder();
        $user = User::find($this->getUserIdentifier());
        $builder->permittedFor($this->getClient()->getIdentifier())
            ->identifiedBy($this->getIdentifier(), true)
            ->issuedAt(time())
            ->canOnlyBeUsedAfter(time())
            ->expiresAt($this->getExpiryDateTime()->getTimestamp())
            ->relatedTo($this->getUserIdentifier())
            ->withClaim('scopes', [])
            ->withClaim('id', $user->id)
            ->withClaim('name', $user->name)
            ->withClaim('email', $user->email);
        return $builder
            ->getToken(new Sha256(), new Key($privateKey->getKeyPath(), $privateKey->getPassPhrase()));
    }

    /**
     * setting up private key
     *
     * @var CryptKey $privateKey
     */
    public function setPrivateKey(CryptKey $privateKey)
    {
        $this->privateKey = $privateKey;
    }

    /**
     * string
     *
     */
    public function __toString()
    {
        return (string) $this->convertToJWT($this->privateKey);
    }
}
