<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class HomepageController extends Controller
{
    /**
     * The admin landing page.
     *
     * @return Response
     */
    public function admin()
    {
        return $this->render('admin.html.twig');
    }
}
