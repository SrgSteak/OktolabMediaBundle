<?php

namespace Oktolab\MediaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oktolab\MediaBundle\Entity\Media;
use Oktolab\MediaBundle\Form\MediaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Media controller.
 *
 * @Route("/oktolab_media/media")
 */
class MediaController extends Controller
{

    /**
     * @Route("/index", name="oktolab_media_index")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $results = $request->query->get('results', 20);

        $em = $this->getDoctrine()->getManager();
        $class = $this->container->getParameter("oktolab_media.media_class");
        $dql = "SELECT m, e FROM ".$class." m LEFT JOIN m.episode e ORDER BY m.updatedAt DESC";
        $query = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $medias = $paginator->paginate(
            $query,
            $page,
            $results
        );

        return ['medias' => $medias];
    }

    /**
     * @Route("/media_for_episode/{uniqID}", name="oktolab_media_media_for_episode")
     * @Template()
     */
    public function mediaForEpisodeAction($uniqID)
    {
        $episode = $this->get('oktolab_media')->getEpisode($uniqID);
        return ['medias' => $episode->getMedia(), 'episode' => $episode];
    }

    /**
     * @Route("/new/{uniqID}", name="oktolab_media_new_media")
     * @Template()
     */
    public function newAction(Request $request, $uniqID)
    {
        $episode = $this->get('oktolab_media')->getEpisode($uniqID);
        $media = new Media();
        $media->setEpisode($episode);
        $form = $this->createForm(MediaType::class, $media);
        $form->add('submit', SubmitType::class, ['label' => 'oktolab_media_media_create_button', 'attr' => ['class' => 'btn btn-primary']]);

        if ($request->getMethod() == "POST") { //sends form
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($media);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'oktolab_media_success_create_media');

                return $this->redirect($this->generateUrl('oktolab_media_show_media', ['media' => $media->getId()]));
            } else {
                $this->get('session')->getFlashBag()->add('error', 'oktolab_media_error_create_media');
            }
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/{media}", name="oktolab_media_show_media")
     * @Template()
     */
    public function showAction(Media $media)
    {
        return ['media' => $media];
    }

    /**
     * @Route("/{media}/edit", name="oktolab_media_edit_media")
     * @Template()
     */
    public function editAction(Request $request, Media $media)
    {
        $form = $this->createForm(MediaType::class, $media);
        $form->add('submit', SubmitType::class, ['label' => 'oktolab_media_edit_media_button', 'attr' => ['class' => 'btn btn-primary']]);
        $form->add('delete', SubmitType::class, ['label' => 'oktolab_media_delete_media_button', 'attr' => ['class' => 'btn btn-danger']]);


        if ($request->getMethod() == "POST") { //sends form
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                if ($form->get('delete')->isClicked()) { //delete media
                    $uniqID = $media->getEpisode()->getUniqID();
                    if ($media->getAsset()) {
                        if ($media->getAsset() !== $media->getEpisode()->getVideo()) {
                            $this->get('bprs.asset_helper')->deleteAsset($media->getAsset());
                        } else {
                            $media->setAsset();
                        }
                    }
                    $em->remove($media);
                    $em->flush();
                    $this->get('session')->getFlashBag()->add('success', 'oktolab_media_success_delete_media');
                    return $this->redirect($this->generateUrl('oktolab_episode_show', ['uniqID' => $uniqID]));
                } else { //persist media
                    $em->persist($media);
                    $em->flush();
                    $this->get('session')->getFlashBag()->add('success', 'oktolab_media_success_update_media');
                    return $this->redirect($this->generateUrl('oktolab_media_show_media', ['media' => $media->getId()]));
                }
            } else {
                $this->get('session')->getFlashBag()->add('error', 'oktolab_media_error_update_media');
            }
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/{media}/set_as_master", name="oktolab_media_set_as_master")
     */
    public function setAsMasterAction(Request $request, Media $media)
    {
        if ($media->getProgress() == 100) {
            $helper = $this->get('oktolab_media_helper');
            $helper->deleteVideo($media->getEpisode());
            $media->getEpisode()->setVideo($media->getAsset());
            $em = $this->getDoctrine()->getManager();
            $em->persist($media->getEpisode());
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                'oktolab_media_success_set_media_as_video'
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'success',
                'oktolab_media_error_set_media_as_video'
            );
        }
        return $this->redirect(
            $this->generateUrl(
                'oktolab_media_show_media',
                ['media' => $media->getId()]
            )
        );
    }

    /**
     * @Route("/{media}/progress", name="oktolab_media_progress_media")
     * @Template()
     */
    public function progressAction(Request $request, Media $media)
    {
        return new JsonResponse($media->getProgress());
        return ['media' => $media];
    }
}
