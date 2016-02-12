<?php

namespace AppBundle\Interfaces;

use Symfony\Component\HttpFoundation\Request;

interface RestfulCrudController
{
    public function getAction($mixed);

    public function deleteAction($mixed);

    public function putAction(Request $request);

    public function patchAction(Request $request);
}
