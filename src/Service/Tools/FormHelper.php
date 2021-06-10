<?php

namespace App\Service\Tools;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;

class FormHelper
{

    /**
     * @param Form $form
     * @return array
     */
    public static function getFormErrors(Form $form) : array
    {
        $errors = [];

        // Global
        foreach ($form->getErrors() as $error) {
            $errors[$form->getName()][] = $error->getMessage();
        }

        // Fields
        self::getChildErrors($form, $errors);
        return $errors;
    }

    private static function getChildErrors(FormInterface $form, array &$errors)
    {
        foreach ($form as $child) {
            if (!$child->isValid()) {
                foreach ($child->getErrors() as $error) {
                    $errors[$child->getName()][] = $error->getMessage();
                }
            }

            self::getChildErrors($child, $errors);
        }
    }
}
