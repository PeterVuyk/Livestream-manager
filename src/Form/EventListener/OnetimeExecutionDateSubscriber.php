<?php
declare(strict_types=1);

namespace App\Form\EventListener;

use App\Entity\StreamSchedule;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnetimeExecutionDateSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        /** @var StreamSchedule $streamSchedule */
        $streamSchedule = $event->getData();
        $form = $event->getForm();

        if (!$streamSchedule || $streamSchedule->isRecurring() === false) {
            $form->add(
                'onetimeExecutionDate',
                DateTimeType::class,
                [
                    'label' => 'stream.form.label.detail.onetime_execution_date',
                    'translation_domain' => 'schedule_create',
                ]
            );
        }
    }
}
