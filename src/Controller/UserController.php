<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Customer;
use App\Repository\UserRepository;
use App\Repository\CustomerRepository;
use App\Exception\EntityNotFoundException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use App\Exception\ResourceValidationException;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use Doctrine\Common\Annotations\AnnotationReader;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;


class UserController extends FOSRestController
{
    private $classMetadataFactory;
    private $encoder;
    private $userRepository;
    private $em;
    private $customerRepository;
    public function __construct(
        UserRepository $userRepository,
        ObjectManager $em,
        CustomerRepository $customerRepository
    ) {
        $this->classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $this->encoder = new JsonEncoder();
        $this->userRepository = $userRepository;
        $this->customerRepository = $customerRepository;
        $this->em = $em;
    }
     /**
     * @Rest\Get(
     *     path = "/users/{id}",
     *     name = "app_user_show",
     *     requirements = {"id"="\d+"}
     * )
     *  @Rest\View(statusCode = 200, serializerGroups={"show_user"})
     */
    public function getUserAction($id):Response
    {  
        $user = $this->userRepository->findOneBy(['id' => $id]);
        
        if (!$user) {
            throw new EntityNotFoundException("This user with Id: $id is not found, try with an other user id please");   
        }
        $serializer = $this->getSerializer();
        $data = $serializer->serialize($user, 'json', ['groups' => 'show_user']);
        $response = new Response($data,Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    /**
     * @Rest\Post(
     *     path = "/users",
     *     name = "app_user_create",
     * )
     * 
     * @Rest\View(StatusCode = Response::HTTP_CREATED, serializerGroups={"create_user"})
     * @ParamConverter(
     *     "user",
     *     converter="fos_rest.request_body",
     *     options={
     *         "validator"={ "groups"="creqate_user" }
     *     }
     * )
    */
    public function postUserAction(User $user, Request $request,ConstraintViolationList $violations)
    {
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ResourceValidationException($message);
        }
        $customer = new Customer();
        $data = $request->getContent();
        $user = $this->get('serializer')
        ->deserialize($data,User::class, 'json');
        $customer = $this->customerRepository->findOneByUsername($user->getCustomer()->getUsername());
        $user->setCustomer($customer);
        
        $this->em->persist($user);
        $this->em->flush();
        return $this->view(
            $user,
            Response::HTTP_CREATED,
            ['Location' => $this->generateUrl(
                'app_user_show', 
                ['id' => $user->getId(),
                UrlGeneratorInterface::ABSOLUTE_URL]
            )]
        );
    }
    /**
     * Undocumented function
     *
     * @param integer $id
     * @return Response
     * @Rest\Delete(
     *     path = "/users/{id}",
     *     name = "app_user_create",
     * )
    */
    
    public function getSerializer()
    {
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER =>
             function ($object, $format, $context) { return $object->getId();},];
            $normalizer = new ObjectNormalizer($this->classMetadataFactory, null, null, null, null, null, $defaultContext);
            $serializer = new Serializer([$normalizer], [$this->encoder]);
            return $serializer;
    }
    
}
