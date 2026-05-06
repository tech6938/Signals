<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AboutController extends Controller
{
    /**
     * Display the about page for WebView
     * 
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $companyData = $this->getCompanyData();
        
        return view('webview.about', compact('companyData'));
    }

    /**
     * Get company information as JSON for API calls
     * 
     * @return JsonResponse
     */
    public function getCompanyInfo(): JsonResponse
    {
        $companyData = $this->getCompanyData();
        
        return response()->json([
            'success' => true,
            'data' => $companyData
        ]);
    }

    /**
     * Get company data array
     * 
     * @return array
     */
    private function getCompanyData(): array
    {
        return [
            'company_name' => config('app.name', 'Your Company Name'),
            'tagline' => 'Excellence in Every Solution',
            'description' => [
                'Welcome to ' . config('app.name', 'Your Company') . ', where innovation meets excellence. Founded with a vision to transform businesses through cutting-edge technology solutions, we have been at the forefront of digital transformation for over a decade.',
                'Our team of dedicated professionals brings together years of experience in software development, digital marketing, and business consulting. We believe in creating solutions that not only meet your immediate needs but also scale with your growing business requirements.',
                'At ' . config('app.name', 'Your Company') . ', we are committed to delivering exceptional value to our clients through innovative approaches, quality deliverables, and unwavering support. Your success is our success, and we work tirelessly to ensure your business goals are achieved.'
            ],
            'services' => [
                [
                    'title' => 'Web Development',
                    'description' => 'Custom web applications built with modern frameworks and technologies to drive your business forward.',
                    'icon' => '💻'
                ],
                [
                    'title' => 'Mobile Apps',
                    'description' => 'Native and cross-platform mobile applications that provide seamless user experiences across all devices.',
                    'icon' => '📱'
                ],
                [
                    'title' => 'Cloud Solutions',
                    'description' => 'Scalable cloud infrastructure and services to optimize your operations and reduce costs.',
                    'icon' => '☁️'
                ],
                [
                    'title' => 'Digital Marketing',
                    'description' => 'Strategic marketing campaigns to boost your online presence and drive meaningful engagement.',
                    'icon' => '📈'
                ],
                [
                    'title' => 'Consulting',
                    'description' => 'Expert business and technology consulting to guide your digital transformation journey.',
                    'icon' => '🤝'
                ],
                [
                    'title' => 'Support & Maintenance',
                    'description' => 'Ongoing support and maintenance services to keep your systems running smoothly 24/7.',
                    'icon' => '🔧'
                ]
            ],
            'contact' => [
                'email' => 'info@yourcompany.com',
                'phone' => '+1 (555) 123-4567',
                'address' => '123 Business Street, Suite 100',
                'city_state' => 'City, State 12345',
                'website' => 'www.yourcompany.com',
                'business_hours' => [
                    'weekdays' => 'Monday - Friday: 9:00 AM - 6:00 PM',
                    'saturday' => 'Saturday: 10:00 AM - 4:00 PM',
                    'sunday' => 'Sunday: Closed'
                ],
                'social_media' => [
                    'facebook' => 'https://facebook.com/yourcompany',
                    'twitter' => 'https://twitter.com/yourcompany',
                    'linkedin' => 'https://linkedin.com/company/yourcompany',
                    'instagram' => 'https://instagram.com/yourcompany'
                ]
            ]
        ];
    }

    /**
     * Get specific service information
     * 
     * @param string $service
     * @return JsonResponse
     */
    public function getServiceInfo(string $service): JsonResponse
    {
        $companyData = $this->getCompanyData();
        $services = collect($companyData['services']);
        
        $serviceInfo = $services->firstWhere('title', ucfirst(str_replace('-', ' ', $service)));
        
        if (!$serviceInfo) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $serviceInfo
        ]);
    }
}