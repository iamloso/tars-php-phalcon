<?php
namespace PFrame\Libs\Models;

use PFrame\Libs\Extensions\InterfaceModel;

class Test extends InterfaceModel
{

    /**
     * Initialize method for model.
     *
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource($this->getSource());

    }

    public function getSource()
    {
        return 'test';
    }
}