<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class TaskController
 * @Rest\Route("/api")
 */
class TaskController extends AbstractFOSRestController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * TaskController constructor.
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @Rest\Get("/listtasks")
     */
    public function listTasksAction()
    {
        $repository = $this->getDoctrine()->getRepository(Task::class);
        $tasks = $repository->findall();

        return JsonResponse::fromJsonString($this->serializer->serialize($tasks, "json"), Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/gettask")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function getTaskAction(Request $request)
    {
        $id = $request->get('id');
        $repository = $this->getDoctrine()->getRepository(Task::class);
        $task = $repository->find($id);

        if (!is_null($task)) {
            return JsonResponse::fromJsonString($this->serializer->serialize($task, "json"), Response::HTTP_OK);
        }

        return new Response($this->serializer->serialize(['response' => 'La tarea no existe'], "json"), Response::HTTP_NOT_FOUND);
    }

    /**
     * @Rest\Post("/addtask")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function addTaskAction(Request $request)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $task_param = $request->request->get('task', null);
        $date_param = $request->request->get('date', null);
        $form->submit(['task' => $task_param, 'date' => $date_param]);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            return new JsonResponse($this->serializer->serialize(['response' => "Task added successfully"], "json"), Response::HTTP_CREATED, [], true);
        }
        return new Response($this->serializer->serialize($form->getErrors(), "json"), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Post("/deletetask")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function deleteTaskAction(Request $request)
    {
        $id_param = $request->request->get('id', null);

        if (is_null($id_param))
            return new Response($this->serializer->serialize(['response' => 'Falta el parámetro ID'], "json"), Response::HTTP_BAD_REQUEST);

        $repository = $this->getDoctrine()->getRepository(Task::class);
        $task = $repository->find($id_param);

        if (!is_null($task)) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($task);
            $em->flush();

            return new JsonResponse($this->serializer->serialize(['response' => "Task deleted successfully"], "json"), Response::HTTP_CREATED, [], true);
        }

        return new Response($this->serializer->serialize(['response' => 'La tarea no existe'], "json"), Response::HTTP_NOT_FOUND);
    }

    /**
     * @Rest\Post("/updatetask")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function updateTaskAction(Request $request)
    {
        $id_param = $request->request->get('id', null);
        $task_param = $request->request->get('task', null);
        $date_param = $request->request->get('date', null);

        $repository = $this->getDoctrine()->getRepository(Task::class);
        $task = $repository->find($id_param);

        if (!is_null($task)) {
            if (!is_null($task_param))
                $task->setTask($task_param);

            if (!is_null($date_param))
                $task->setDate($date_param);

            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            return new JsonResponse($this->serializer->serialize(['response' => "Task updated successfully"], "json"), Response::HTTP_CREATED, [], true);
        }

        return new Response($this->serializer->serialize(['response' => 'La tarea no existe'], "json"), Response::HTTP_NOT_FOUND);
    }
}
