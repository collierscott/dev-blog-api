<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\Exception\ValidationException;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Image;
use App\Form\ImageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UploadImageAction
 */
class UploadImageAction
{
    /** @var FormFactoryInterface $formFactory */
    private $formFactory;

    /** @var EntityManagerInterface $manager */
    private $manager;

    /** @var ValidatorInterface $validator */
    private $validator;

    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $manager,
        ValidatorInterface $validator
    )
    {
        $this->formFactory = $formFactory;
        $this->manager = $manager;
        $this->validator = $validator;
    }

    public function __invoke(Request $request)
    {
        // Create new image instance
        $image = new Image();
        // validate the form
        $form = $this->formFactory->create(ImageType::class, $image);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            // Persist the new image entity
            $this->manager->persist($image);
            $this->manager->flush();

            // File has to be set to null due to size because it is binary
            $image->setFile(null);

            return $image;
        }

        // uploading is done for us in the background by VichUploader


        // Throw validation exception error
        throw new ValidationException(
            $this->validator->validate($image)
        );

    }
}