<?php

namespace App\View\Components;

use Illuminate\View\Component;

class HealthEducation extends Component
{
    public function __construct()
    {
        //
    }

    public function render()
    {
        $educationalResources = [
            [
                'title' => 'Understanding Basic Health',
                'description' => 'Learn about fundamental health concepts and practices.',
                'type' => 'article',
                'icon' => 'book-open'
            ],
            [
                'title' => 'Nutrition Guidelines',
                'description' => 'Essential information about balanced diet and nutrition.',
                'type' => 'guide',
                'icon' => 'apple-alt'
            ],
            [
                'title' => 'Exercise Fundamentals',
                'description' => 'Basic exercises and physical activity recommendations.',
                'type' => 'video',
                'icon' => 'running'
            ],
            [
                'title' => 'Mental Health Awareness',
                'description' => 'Resources for understanding and maintaining mental health.',
                'type' => 'article',
                'icon' => 'brain'
            ]
        ];

        return view('components.health-education', [
            'resources' => $educationalResources
        ]);
    }
} 