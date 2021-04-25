<?php


namespace App\Datatable;


use App\Entity\Notification;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\BooleanColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\DateTimeColumn;
use Sg\DatatablesBundle\Datatable\Filter\SelectFilter;
use Sg\DatatablesBundle\Datatable\Style;

class
NotificationDatatable extends \Sg\DatatablesBundle\Datatable\AbstractDatatable
{

    /**
     * @inheritDoc
     */
    public function buildDatatable(array $options = [])
    {
        $this->language->set(array(
            'cdn_language_by_locale' => true,
        ));
        $this->ajax->set([
            // send some extra example data
            'data' => ['data1' => 1, 'data2' => 2],
            // cache for 10 pages
            'pipeline' => 10
        ]);
        $this->options->set([
            'classes' => Style::BOOTSTRAP_4_STYLE,
            'stripe_classes' => ['strip1', 'strip2', 'strip3'],
            'individual_filtering' => true,
            'individual_filtering_position' => 'head',
            'order' => [
                [
                    0, 'asc'
                ]
            ],
            'order_cells_top' => true,
            'search_in_non_visible_columns' => true,
        ]);

        $this->columnBuilder
            ->add('id', Column::class, [
                "title" => $this->translator->trans("notification.label.id", [], "OurTripsTrans"),
                'searchable' => true,
                'orderable' => true,
                "width" => "50px"
            ])
            ->add("created_at", DateTimeColumn::class, [
                "title" => $this->translator->trans("notification.label.created_at", [], "OurTripsTrans")
            ])
            ->add("content", Column::class, [
                "title" => $this->translator->trans("notification.label.content", [], "OurTripsTrans")
            ])
            ->add("isRead", BooleanColumn::class, [
                "title" => $this->translator->trans("notification.label.isRead", [], "OurTripsTrans"),
                'visible' => true,
                "true_icon" => "fa fa-check text-success",
                "false_icon" => "fa fa-times text-danger",
                'filter' => array(SelectFilter::class, [
                    'classes' => 'test1 test2',
                    'search_type' => 'eq',
                    'multiple' => false,
                    'select_options' => [
                        '' => 'Tous',
                        '1' => 'lu',
                        '0' => 'Non lu'
                    ]])
            ])
            ->add(null, ActionColumn::class, [
                'title' => 'Actions',
                'start_html' => '<div class="start_actions">',
                'end_html' => '</div>',
                'actions' => [
                    [
                        'route' => 'admin_notification_read',
                        'route_parameters' => [
                            'id' => 'id'
                        ],
                        'icon' => 'fa fa-toggle-off',
                        'attributes' => [
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('notification.read', [], 'OurTripsTrans'),
                            'class' => 'btn btn-danger btn-xs',
                            'role' => 'button'
                        ],
                        'render_if' => function ($row) {
                            return !$row['isRead'];
                        },
                    ],
                    [
                        'route' => 'admin_notification_read',
                        'route_parameters' => [
                            'id' => 'id'
                        ],
                        'icon' => 'fa fa-toggle-on',
                        'attributes' => [
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('notification.notread', [], 'OurTripsTrans'),
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button'
                        ],
                        'render_if' => function ($row) {
                            return $row['isRead'];
                        },
                    ],
                    [
                        'route' => 'admin_notification_show',
                        'label' => null,
                        'route_parameters' => [
                            'id' => 'id',
                            '_format' => 'html',
                            '_locale' => 'fr'
                        ],
                        'icon' => 'fa fa-eye',
                        'attributes' => [
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans("form.notification.label.show", [], "OurTripsTrans"),
                            'role' => 'button',
                            'class' => "btn btn-success btn-xs"
                        ],
                        'start_html' => '<div class="start_show_action">',
                        'end_html' => '</div>',
                    ]
                ]
            ]);;
    }

    /**
     * @inheritDoc
     */
    public function getEntity()
    {
        return Notification::class;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return "notification_datatable";
    }
}
