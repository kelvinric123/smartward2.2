<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HealthEducationController extends Controller
{
    public function index()
    {
        return view('health.index');
    }

    public function resources(Request $request, $type, $title)
    {
        // Sample resource content based on type and title
        $resourceContent = $this->getResourceContent($type, $title);
        
        return view('health.resource', [
            'type' => $type,
            'title' => Str::title(str_replace('-', ' ', $title)),
            'content' => $resourceContent
        ]);
    }

    public function contact()
    {
        return view('health.contact');
    }

    public function patientPanelView($bedId)
    {
        $bed = \App\Models\Bed::with(['admissions.patient', 'ward'])->findOrFail($bedId);
        
        $currentAdmission = $bed->admissions->first();
        $patientName = $currentAdmission && $currentAdmission->patient ? $currentAdmission->patient->full_name : 'No Patient';
        $patientMRN = $currentAdmission && $currentAdmission->patient ? $currentAdmission->patient->mrn : 'N/A';
        
        return view('patient-panel.health-education', [
            'bed' => $bed,
            'currentAdmission' => $currentAdmission,
            'patientName' => $patientName,
            'patientMRN' => $patientMRN
        ]);
    }

    private function getResourceContent($type, $title)
    {
        // In a real application, this would come from a database
        $resources = [
            'understanding-basic-health' => [
                'content' => '<p class="mb-4">Health is a state of complete physical, mental, and social well-being and not merely the absence of disease or infirmity.</p>
                <h3 class="text-xl font-bold mb-2">Key Health Concepts</h3>
                <ul class="list-disc pl-5 mb-4">
                    <li>Regular health check-ups</li>
                    <li>Balanced nutrition</li>
                    <li>Regular physical activity</li>
                    <li>Adequate sleep</li>
                    <li>Stress management</li>
                </ul>
                <p class="mb-4">Understanding these basic concepts can help you maintain good health and prevent various diseases.</p>'
            ],
            'nutrition-guidelines' => [
                'content' => '<p class="mb-4">Good nutrition is an important part of leading a healthy lifestyle.</p>
                <h3 class="text-xl font-bold mb-2">Dietary Guidelines</h3>
                <ul class="list-disc pl-5 mb-4">
                    <li>Eat a variety of foods</li>
                    <li>Maintain a healthy weight</li>
                    <li>Choose a diet low in fat, saturated fat, and cholesterol</li>
                    <li>Choose a diet with plenty of vegetables, fruits, and grain products</li>
                    <li>Use sugars only in moderation</li>
                    <li>Use salt and sodium only in moderation</li>
                </ul>
                <p class="mb-4">Following these guidelines can help reduce the risk of chronic diseases and maintain good health.</p>'
            ],
            'exercise-fundamentals' => [
                'content' => '<p class="mb-4">Regular physical activity is one of the most important things you can do for your health.</p>
                <h3 class="text-xl font-bold mb-2">Benefits of Exercise</h3>
                <ul class="list-disc pl-5 mb-4">
                    <li>Weight control</li>
                    <li>Reduced risk of cardiovascular disease</li>
                    <li>Strengthened bones and muscles</li>
                    <li>Improved mental health and mood</li>
                    <li>Improved ability to do daily activities</li>
                </ul>
                <p class="mb-4">Aim for at least 150 minutes of moderate-intensity aerobic activity or 75 minutes of vigorous-intensity aerobic activity a week.</p>'
            ],
            'mental-health-awareness' => [
                'content' => '<p class="mb-4">Mental health includes our emotional, psychological, and social well-being. It affects how we think, feel, and act.</p>
                <h3 class="text-xl font-bold mb-2">Taking Care of Your Mental Health</h3>
                <ul class="list-disc pl-5 mb-4">
                    <li>Connect with others</li>
                    <li>Stay positive</li>
                    <li>Get physically active</li>
                    <li>Help others</li>
                    <li>Get enough sleep</li>
                    <li>Develop coping skills</li>
                </ul>
                <p class="mb-4">If you experience mental health problems, seek professional help. Mental health conditions are common and treatable.</p>'
            ]
        ];

        $slug = Str::slug(str_replace('-', ' ', $title));
        
        return $resources[$slug]['content'] ?? 'Content not available.';
    }
} 