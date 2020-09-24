<?php


namespace App\Security;


use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    //TODO: The AccessDeniedHandler redirect the user depending on their roles. It might be not the best solution
    private $security;
    private $urlGenerator;
    public function __construct(Security $security, UrlGeneratorInterface $urlGenerator)
    {
        $this->security=$security;
        $this->urlGenerator=$urlGenerator;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        if ($this->security->isGranted('ROLE_USER'))return new RedirectResponse($this->urlGenerator->generate('index'));
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }
}