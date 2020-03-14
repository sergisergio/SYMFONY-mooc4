<?php

namespace App\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller used to manage blog contents in the backend.
 *
 * @Route("/admin/post")
 * @IsGranted("ROLE_ADMIN")
 */
class BlogController extends AbstractController
{
    // ...
}
