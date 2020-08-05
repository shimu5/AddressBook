<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contacts;
use AppBundle\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class AddressBookController extends Controller
{
    /**
     * @Route("/", name="contact_list")
     */
    public function listAction()
    {
        $contact_list = $this->getDoctrine()
            ->getRepository('AppBundle:Contacts')
            ->findAll();

        return $this->render('addressbook/index.html.twig',array('contacts' => $contact_list));
    }

    /**
     * @Route("/contacts/create", name="addrbook_create")
     */
    public function createAction(Request $request,FileUploader $fileUploader)
    {
        $contact = new Contacts;
        $form = $this->createFormBuilder($contact)
        ->add('firstName', TextType::class, array('attr'=>array('class' =>'form-control', 'style'=>'margin-bottom:15px')))
            ->add('lastName', TextType::class, array('attr'=>array('class' =>'form-control', 'style'=>'margin-bottom:15px')))
            ->add('streetName', TextType::class, array('attr'=>array('class' =>'form-control', 'style'=>'margin-bottom:15px')))
            ->add('streetNo', IntegerType::class, array('attr'=>array('class' =>'form-control', 'style'=>'margin-bottom:15px')))
            ->add('zip', IntegerType::class, array('attr'=>array('class' =>'form-control', 'style'=>'margin-bottom:15px')))
            ->add('city', TextType::class, array('attr'=>array('class' =>'form-control', 'style'=>'margin-bottom:15px')))
            ->add('country', TextType::class, array('attr'=>array('class' =>'form-control', 'style'=>'margin-bottom:15px')))
            ->add('phone', TextType::class, array('attr'=>array('class' =>'form-control', 'style'=>'margin-bottom:15px')))
            ->add('dob', BirthdayType::class, array('attr'=>array('class' =>'form-control datepicker-here', 'style'=>'margin-bottom:15px', 'data-position'=>'right top')))
            ->add('email', TextType::class, array('attr'=>array('class' =>'form-control', 'style'=>'margin-bottom:15px')))
            ->add('picture', FileType::class, [
                'attr'=>array('class' =>'form-control-file', 'style'=>'margin-bottom:15px'),
                'label' => 'Photo',
                
                // unmapped means that this field is not associated to any entity property
                 'mapped' => false,
                
                // make it optional so you don't have to re-upload the file
                // every time you edit the Product details
                'required' => false,
                
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                /*'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Please upload a JPG/PNG',
                    ])
                ]*/
            ])
            ->add('save', SubmitType::class, ['attr'=>array('class' =>'btn btn-success'),'label' => 'Create Contact'])
            ->getForm();
            
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $contactPicFile = $form->get('picture')->getData();
            if ($contactPicFile) {
                $contactPicFileName = $fileUploader->upload($contactPicFile);
                $contact->setPicture($contactPicFileName);
            }
            
            
            $fname = $form['firstName']->getData();
            $lastName = $form['lastName']->getData();
            $streetName = $form['streetName']->getData();
            $streetNo = $form['streetNo']->getData();
            $zip = $form['zip']->getData();
            $city = $form['city']->getData();
            $country = $form['country']->getData();
            $phone = $form['phone']->getData();
            $dob = $form['dob']->getData();
            $email = $form['email']->getData();
            //$picture = $form['picture']->getData();
            
            $contact->setFirstName($fname);
            $contact->setLastName($lastName);
            $contact->setStreetName($streetName);
            $contact->setStreetNo($streetNo);
            $contact->setZip($zip);
            $contact->setCity($city);
            $contact->setCountry($country);
            $contact->setDob($dob);
            $contact->setPhone($phone);
            $contact->setEmail($email);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();
            
            $this->addFlash('notice', 'Contact Added Success');
            return $this->redirectToRoute('contact_list');
            
        }
        return $this->render('addressbook/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/contacts/view/{id}", name="addrbook_details")
     */
    public function detailsAction($id)
    {
        $contact = $this->getDoctrine()
        ->getRepository('AppBundle:Contacts')
        ->find($id);
        
        return $this->render('addressbook/details.html.twig',array('contact' => $contact));
    }

     /**
     * @Route("/contacts/edit/{id}", name="addrbook_edit")
     */
    public function editAction($id, Request $request, FileUploader $fileUploader)
    {  
        $contact = $this->getDoctrine()
        ->getRepository('AppBundle:Contacts')
        ->find($id);
   
        $contact->setFirstName($contact->getFirstName());
        $contact->setLastName($contact->getLastName());
        $contact->setStreetName($contact->getStreetName());
        $contact->setStreetNo($contact->getStreetNo());
        $contact->setZip($contact->getZip());
        $contact->setCity($contact->getCity());
        $contact->setCountry($contact->getCountry());
        $contact->setDob($contact->getDob());
        $contact->setPhone($contact->getPhone());
        $contact->setEmail($contact->getEmail());
        $contact->setPicture($contact->getPicture());
    
        $form = $this->createFormBuilder($contact)
        ->add('firstName', TextType::class, array('attr'=>array('class' =>'form-control', 'style'=>'margin-bottom:15px')))
        ->add('lastName', TextType::class, array('attr'=>array('class' =>'form-control', 'style'=>'margin-bottom:15px')))
        ->add('streetName', TextType::class, array('attr'=>array('class' =>'form-control', 'style'=>'margin-bottom:15px')))
        ->add('streetNo', IntegerType::class, array('attr'=>array('class' =>'form-control', 'style'=>'margin-bottom:15px')))
        ->add('zip', IntegerType::class, array('attr'=>array('class' =>'form-control', 'style'=>'margin-bottom:15px')))
        ->add('city', TextType::class, array('attr'=>array('class' =>'form-control', 'style'=>'margin-bottom:15px')))
        ->add('country', TextType::class, array('attr'=>array('class' =>'form-control', 'style'=>'margin-bottom:15px')))
        ->add('phone', TextType::class, array('attr'=>array('class' =>'form-control', 'style'=>'margin-bottom:15px')))
        ->add('dob', BirthdayType::class, array('attr'=>array('class' =>'form-control', 'style'=>'margin-bottom:15px')))
        ->add('email', TextType::class, array('attr'=>array('class' =>'form-control', 'style'=>'margin-bottom:15px')))
        ->add('picture', FileType::class, [
            'attr'=>array('class' =>'form-control', 'style'=>'margin-bottom:15px'),
            'label' => 'Photo',
            'mapped' => false,
            'required' => false,
          
        ])
        ->add('save', SubmitType::class, ['attr'=>array('class' =>'btn btn-success'),'label' => 'Edit Contact'])
        ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            
            $firstname = $form['firstName']->getData();
            $lastName = $form['lastName']->getData();
            $streetName = $form['streetName']->getData();
            $streetNo = $form['streetNo']->getData();
            $zip = $form['zip']->getData();
            $city = $form['city']->getData();
            $country = $form['country']->getData();
            $phone = $form['phone']->getData();
            $dob = $form['dob']->getData();
            $email = $form['email']->getData();

            
            $em = $this->getDoctrine()->getManager();
            $em->getRepository('AppBundle:Contacts')->find($id);
            
            $contactPicFile = $form->get('picture')->getData();
            
            if ($contactPicFile) {
                if(file_exists("../web/uploads/".$contactPicFile))
                        unlink("../web/uploads/".$contactPicFile);
                $contactPicFileName = $fileUploader->upload($contactPicFile);
                $contact->setPicture($contactPicFileName);
            }
            
            $contact->setFirstName($firstname);
            $contact->setLastName($lastName);
            $contact->setStreetName($streetName);
            $contact->setStreetNo($streetNo);
            $contact->setZip($zip);
            $contact->setCity($city);
            $contact->setCountry($country);
            $contact->setDob($dob);
            $contact->setPhone($phone);
            $contact->setEmail($email);
                      
            $em->flush();
            
            $this->addFlash(
                'notice',
                'Contact Updated'
                );
            return $this->redirectToRoute('contact_list');
        }
        return $this->render('addressbook/edit.html.twig',array(
            'contact' => $contact,
            'form'=>$form->createView()
        ));
     
    }


     /**
     * @Route("/contacts/delete/{id}", name="addrbook_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $contact = $em->getRepository('AppBundle:Contacts')->find($id);
        $em->remove($contact);
        $em->flush();

        unlink("../web/uploads/".$contact->getPicture());
        
        $this->addFlash(
            'notice',
            'Contact Removed'
            );
        return $this->redirectToRoute('contact_list');
    }
}

