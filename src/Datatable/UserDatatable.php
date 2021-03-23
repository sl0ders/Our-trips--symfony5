<?php


namespace App\Datatable;


use App\Entity\User;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\DateTimeColumn;
use Sg\DatatablesBundle\Datatable\Style;

class UserDatatable extends \Sg\DatatablesBundle\Datatable\AbstractDatatable
{

    /**
     * @inheritDoc
     * @throws \Exception
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
                'title' => 'Id',
                'searchable' => true,
                'orderable' => true,
                "width" => "50px"
            ])->add("firstname", Column::class, [
                "title" => $this->translator->trans("datatable.user.firstname", [], "OurTripsTrans"),
                'searchable' => true,
                'orderable' => true,
                "width" => "125px"
            ])->add("lastname", Column::class, [
                'title' => $this->translator->trans("datatable.user.lastname", [], "OurTripsTrans"),
                'searchable' => true,
                'orderable' => true,
                "width" => "140px"
            ])->add("email", Column::class, [
                'title' => $this->translator->trans("datatable.user.email", [], "OurTripsTrans"),
                'searchable' => true,
                'orderable' => true,
                "width" => "125px"
            ])->add("roles", Column::class, [
                'title' => $this->translator->trans("datatable.user.role", [], "OurTripsTrans"),
                'searchable' => true,
                'orderable' => true,
                "width" => "125px"
            ])->add("createdAt", DateTimeColumn::class, [
                'title' => $this->translator->trans("datatable.user.createdAt", [], "OurTripsTrans"),
                'searchable' => true,
                'orderable' => true,
                "width" => "140px"
            ])
            ->add(null, ActionColumn::class, [
                'title' => 'Actions',
                'start_html' => '<div class="start_actions">',
                'end_html' => '</div>',
                'actions' => [
                    [
                        'route' => 'admin_user_show',
                        'label' => $this->translator->trans("form.user.label.show", [], "OurTripsTrans"),
                        'route_parameters' => [
                            'id' => 'id',
                            '_format' => 'html',
                            '_locale' => 'fr'
                        ],
                        'attributes' => [
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans("form.user.label.show", [], "OurTripsTrans"),
                            'class' => 'btn btn-primary btn-xs show-news',
                            'role' => 'button'
                        ],
                        'start_html' => '<div class="start_show_action">',
                        'end_html' => '</div>',
                    ],[
                        'route' => 'admin_user_edit',
                        'label' => $this->translator->trans("form.user.label.edit", [], "OurTripsTrans"),
                        'route_parameters' => [
                            'id' => 'id',
                            '_format' => 'html',
                            '_locale' => 'fr'
                        ],
                        'attributes' => [
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans("form.user.label.edit", [], "OurTripsTrans"),
                            'class' => 'btn btn-warning btn-xs',
                            'role' => 'button'
                        ],
                        'start_html' => '<div class="start_show_action">',
                        'end_html' => '</div>',
                    ]
                ]
            ]);
    }

    /**
     * @inheritDoc
     */
    public function getEntity(): string
    {
        return User::class;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return "user_datatable";
    }
}
