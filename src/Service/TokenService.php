<?php



namespace App\Service;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TokenService extends AbstractController
{

    public function __construct(
        private JWTTokenManagerInterface $jWTTokenManager
    ){}

    /**
     * Generates a JWT token for a specific user and purpose.
     *
     * This method creates a JWT token with a custom payload including the user's email,
     * the intended purpose of the token, and an expiration timestamp.
     *
     * @param User   $user   The user object for whom the token is generated.
     * @param string $purpose A string describing the purpose of the token (e.g., "password_reset").
     * @param string $ttl    The time-to-live for the token, in a format accepted by DateTimeImmutable (default: "+1 hour").
     *
     * @return string The generated JWT token as a string.
     */
    public function generateTokenFromPayload(User $user, string $purpose, $ttl = "+1 hour"): string
    {
        $payload = [
            'email' => $user->getEmail(),
            'purpose' => $purpose,
            'exp' => (new \DateTimeImmutable($ttl))->getTimestamp(),
        ];

        return $this->jWTTokenManager->createFromPayload($user, $payload);
    }

    /**
    * Parses a JWT token and returns its decoded payload.
    *
    * @param string $token The JWT token to parse.
    *
    * @return array The decoded payload of the token.
    */
    public function tokenParser(string $token)
    {
        return $this->jWTTokenManager->parse($token);
    }
}
