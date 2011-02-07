<?php
/**
 *
 * @category   Mn
 * @package    Mn_Controller
 */


/**
 * @category   Mn
 * @package    Mn_Controller
 */
class Mn_Controller_Action_Helper_P3p extends Zend_Controller_Action_Helper_Abstract
{
    
    /**
     * add P3P header
     *
     */
    public function postDispatch()
    {
        $this->getResponse()->setHeader('P3P', 'CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"', true);
    }
}