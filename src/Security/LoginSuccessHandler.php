<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator, private AuthorizationCheckerInterface $authorizationChecker) {}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        $user = $token->getUser();

        if ($user instanceof User && ($user->isAdmin() or $user->isResponsable())) {
            return new RedirectResponse($this->urlGenerator->generate('accueil'));
        } elseif ($user instanceof User && $user->isEmploye()) {
            return new RedirectResponse($this->urlGenerator->generate('accueil_employe'));
        }

        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }
}
