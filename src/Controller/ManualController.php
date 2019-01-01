<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class ManualController extends Controller
{
    /**
     * @return Response
     */
    public function manualPage()
    {
        return $this->render('manual/manual.html.twig');
    }
}
