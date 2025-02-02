<?php

namespace Hatimeria\ExtJSBundle\Response;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Form as SymfonyForm;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Response representation for symfony processed form
 * 
 * Contains error list if form is not valid or Success
 * 
 * @author Michal Wujas
 */
class Form extends Validation
{
    /**
     * Form instance
     *
     * @var Form
     */
    private $form;
    
    /**
     * New error list
     *
     * @param mixed $mixed form or errors array
     */
    public function __construct(SymfonyForm $form)
    {
        $this->form = $form;
    }
    
    /**
     * List of errors
     *
     * @return array field -> message
     */
    public function getFormatted($errors = null)
    {
        // @todo children recursion
        $list = array();
        
        foreach($this->form->getChildren() as $field) {
            if (!$field->hasErrors()) continue;
            $messages = array();
            foreach($field->getErrors() as $error) {
//                $messages[] = $error->getMessageTemplate();
                $messages[] = str_replace(
                        array_keys($error->getMessageParameters()),
                        $error->getMessageParameters(),
                        $error->getMessageTemplate()
                );
            }
            
            $list[$field->getName()] = $messages;
        }
        
        return $list;
    }    
    
    /**
     * Is form valid ?
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->form->isValid();
    }
    
    /**
     * Form serialize
     * 
     * @return array field -> value or errors
     */
    public function toArray(){
        
        if($this->form->isBound()){
            return $this->getContent();
        }
        else{
            foreach($this->form->getChildren() as $form_field) {
                $fields[$form_field->getName()] = $form_field->getClientData();
            }
            return array(
                    'success' => true,
                    'data' => $fields
                );
        }
    }
}