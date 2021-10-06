<?php


namespace App\Datatable;


use App\Entity\Comment;
use Exception;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\DateTimeColumn;
use Sg\DatatablesBundle\Datatable\Style;

class CommentDatatable extends AbstractDatatable
{

    /**
     * @param array $options
     * @return mixed
     * @throws Exception
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
            ])
            ->add('created_at', DateTimeColumn::class, [
                'title' => $this->translator->trans("datatable.comment.createdAt", [], "OurTripsTrans"),
                'searchable' => true,
                'orderable' => true,
                'type_of_field' => 'integer',
                "width" => "100px"
            ])
            ->add('content', Column::class, [
                'title' => $this->translator->trans("datatable.comment.content", [], "OurTripsTrans"),
                'searchable' => true,
                'orderable' => true,
                'type_of_field' => 'integer',
                'class_name' => 'comment-content',
                "width" => "300px"
            ])
            ->add('enabled', Column::class, [
                'title' => $this->translator->trans("datatable.comment.archived", [], "OurTripsTrans"),
                'visible' => false,
                "width" => "70px",

            ])
            ->add(null, ActionColumn::class, [
                'title' => 'Actions',
                'start_html' => '<div class="start_actions">',
                "width" =>"80px" ,
                'end_html' => '</div>',
                'actions' => [
                    [
                        'route' => 'admin_comment_enabled',
                        'route_parameters' => [
                            'id' => 'id'
                        ],
                        'icon' => 'fa fa-toggle-off',
                        'attributes' => [
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('comment.enabled', [], 'OurTripsTrans'),
                            'class' => 'btn btn-danger btn-xs',
                            'role' => 'button'
                        ],
                        'render_if' => function ($row) {
                            return !$row['enabled'];
                        },
                    ],
                    [
                        'route' => 'admin_comment_enabled',
                        'route_parameters' => [
                            'id' => 'id'
                        ],
                        'icon' => 'fa fa-toggle-on',
                        'attributes' => [
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('comment.disabled', [], 'OurTripsTrans'),
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button'
                        ],
                        'render_if' => function ($row) {
                            return $row['enabled'];
                        },
                    ],
                    [
                        'route' => 'admin_comment_show',
                        'label' => null,
                        'route_parameters' => [
                            'id' => 'id',
                            '_format' => 'html',
                            '_locale' => 'fr'
                        ],
                        'icon' => 'fa fa-eye',
                        'attributes' => [
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans("form.comment.label.show", [], "OurTripsTrans"),
                            'role' => 'button',
                            'class' => "btn btn-success btn-xs"
                        ],
                        'start_html' => '<div class="start_show_action">',
                        'end_html' => '</div>',
                    ]
                ]
            ]);
    }

    /**
     * @return string
     */
    public function getEntity(): string
    {
        return Comment::class;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'comment_datatable';
    }
}
