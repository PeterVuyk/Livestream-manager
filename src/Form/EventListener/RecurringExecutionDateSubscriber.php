<?php
declare(strict_types=1);

namespace App\Form\EventListener;

use App\Entity\StreamSchedule;
use App\Entity\Weekdays;
use App\Form\CreateRecurringScheduleType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RecurringExecutionDateSubscriber implements EventSubscriberInterface
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

        if (!$streamSchedule || $streamSchedule->isRecurring() === true) {
            $form->add(
                'executionDay',
                ChoiceType::class,
                [
                    'choices' => CreateRecurringScheduleType::getDaysOfTheWeek(),
                    'label' => 'stream.form.label.detail.label.weekday_choice',
                    'translation_domain' => 'schedule_create',
                    'required' => true,
                    'placeholder' => 'stream.form.placeholder.weekday_choice',
                    'attr' => ['class' => 'form-control'],
                ]
            );

            $form->add(
                'executionTime',
                TimeType::class,
                [
                    'input' => 'datetime',
                    'label' => 'stream.form.label.detail.label.time',
                    'translation_domain' => 'schedule_create',
                    'data' => new \DateTime(),
                    'required' => true,
                ]
            );
        }
    }
}
