<?php

namespace App\Form;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\File\File; // Importez la classe File de HttpFoundation

class StringToFileTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        // Cette méthode est appelée lorsque le formulaire est rendu.
        // Vous pouvez laisser vide dans ce cas.
        return $value;
    }

    public function reverseTransform($value)
    {
        // Cette méthode est appelée lorsque le formulaire est soumis.
        // Vous devez convertir la chaîne de caractères en objet File.
        if (!$value) {
            return null;
        }

        try {
            return new File($value);
        } catch (\Exception $e) {
            throw new TransformationFailedException(sprintf('Unable to transform the string "%s" into a File object.', $value));
        }
    }
}
