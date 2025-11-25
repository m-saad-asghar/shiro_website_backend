<?php

namespace App\Http\Controllers\Api\StaticPage;


use App\Http\Controllers\Controller;
use App\Http\Resources\Model\AboutUsResource;
use App\Http\Resources\Model\BlogCategoryResource;
use App\Http\Resources\Model\BlogResource;
use App\Http\Resources\Model\ContactUsResource;
use App\Http\Resources\Model\CurrencyResource;
use App\Http\Resources\Model\FaqResource;
use App\Http\Resources\Model\PrivacyResource;
use App\Http\Resources\Model\RegionResource;
use App\Http\Resources\Model\ReviewResource;
use App\Http\Resources\Model\SliderResource;
use App\Http\Resources\Model\TeamResource;
use App\Http\Resources\Model\TermisConditionResource;
use App\Http\Traits\GeneralTrait;

use App\Models\AboutUs;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\ContactUs;
use App\Models\Currency;
use App\Models\Faq;
use App\Models\Privacy;
use App\Models\Region;
use App\Models\Review;
use App\Models\Slider;
use App\Models\Team;
use App\Models\TermisCondition;
use Illuminate\Http\Request;

class StaticController extends Controller
{
    use GeneralTrait;

    public function allPrivacy(Request $request)
    {
        try {
            $privacy = Privacy::first();

            return $this->apiResponse([
                'privacy' => $privacy ? new PrivacyResource($privacy) : [],
            ]);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }
    }


    public function allTermsAndCondition(Request $request)
    {
        try {
            $terms = TermisCondition::first();

            return $this->apiResponse([
                'terms_condition' => $terms ? new TermisConditionResource($terms) : [],
            ]);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }
    }



    public function allFaqs(Request $request)
    {
        try {
            $faqs = Faq::query();

            if ($request->has('faq_category_id')) {
                $faqs->where('faq_category_id', $request->faq_category_id);
            }

            return $this->apiResponse([
                'faqs' => FaqResource::collection($faqs->get()),
            ]);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }
    }

    public function showFaq(Request $request)
    {
        try {
            $faq = Faq::findOrFail($request->faq_id);
            return $this->apiResponse([
                'faq' => new FaqResource($faq),
            ]);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }
    }



    public function allSliders(Request $request)
    {
        try {
            $page = $request->get('page', 'home');

            $sliders = Slider::where('page', $page)->get();

            return $this->apiResponse([
                'sliders' => SliderResource::collection($sliders),
            ]);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }
    }

    public function contactInfo()
    {
        try {
            $info = ContactUs::first();
            return $this->apiResponse([
                'contact_info' => new ContactUsResource($info),
            ]);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }
    }

    public function allTeam(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $query = Team::orderBy('sort', 'asc');

            // Filter by team type.
            if ($request->filled('team_type')) {
                $query->where('team_type', $request->team_type);
            }

            // If pagination is required.
            if ($request->has('paginate') && $request->paginate == 'true') {
                $team = $query->paginate($perPage);

                return $this->apiResponse([
                    'team' => TeamResource::collection($team),
                    'pagination' => [
                        'current_page' => $team->currentPage(),
                        'total' => $team->total(),
                        'per_page' => $team->perPage(),
                        'last_page' => $team->lastPage(),
                        'from' => $team->firstItem(),
                        'to' => $team->lastItem(),
                        'next_page_url' => $team->nextPageUrl(),
                        'prev_page_url' => $team->previousPageUrl(),
                    ],
                ]);
            }

            // Without pagination (the old way).
            $team = $query->get();

            // Group the team by type.
            $groupedTeam = $team->groupBy('team_type');

            return $this->apiResponse([
                'team' => TeamResource::collection($team),
                'team_by_type' => [
                    'management' => TeamResource::collection($groupedTeam->get('management', collect())),
                    'brokers' => TeamResource::collection($groupedTeam->get('brokers', collect())),
                ],
                'total_management' => $groupedTeam->get('management', collect())->count(),
                'total_brokers' => $groupedTeam->get('brokers', collect())->count(),
            ]);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }
    }

    public function showTeam(Request $request)
    {
        try {
            $member = Team::findOrFail($request->team_id);

            return $this->apiResponse([
                'team' => new TeamResource($member),
            ]);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }
    }

    public function managementTeam(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $query = Team::management()->orderBy('sort', 'asc');

            // If pagination is required.
            if ($request->has('paginate') && $request->paginate == 'true') {
                $management = $query->paginate($perPage);

                return $this->apiResponse([
                    'management_team' => TeamResource::collection($management),
                    'pagination' => [
                        'current_page' => $management->currentPage(),
                        'total' => $management->total(),
                        'per_page' => $management->perPage(),
                        'last_page' => $management->lastPage(),
                        'from' => $management->firstItem(),
                        'to' => $management->lastItem(),
                        'next_page_url' => $management->nextPageUrl(),
                        'prev_page_url' => $management->previousPageUrl(),
                    ],
                ]);
            }

            // Without pagination.
            $management = $query->get();

            return $this->apiResponse([
                'management_team' => TeamResource::collection($management),
                'total' => $management->count(),
            ]);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }
    }

    public function brokersTeam(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $query = Team::brokers()->orderBy('sort', 'asc');

            // If pagination is required.
            if ($request->has('paginate') && $request->paginate == 'true') {
                $brokers = $query->paginate($perPage);

                return $this->apiResponse([
                    'brokers_team' => TeamResource::collection($brokers),
                    'pagination' => [
                        'current_page' => $brokers->currentPage(),
                        'total' => $brokers->total(),
                        'per_page' => $brokers->perPage(),
                        'last_page' => $brokers->lastPage(),
                        'from' => $brokers->firstItem(),
                        'to' => $brokers->lastItem(),
                        'next_page_url' => $brokers->nextPageUrl(),
                        'prev_page_url' => $brokers->previousPageUrl(),
                    ],
                ]);
            }

            // Without pagination.
            $brokers = $query->get();

            return $this->apiResponse([
                'brokers_team' => TeamResource::collection($brokers),
                'total' => $brokers->count(),
            ]);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }
    }


    public function allAboutUs()
    {
        try {
            // Get the first record from the AboutUs table, or null if it doesn't exist.
            $aboutUs = AboutUs::first();

            // If we found the record, use the Resource, otherwise return an empty array.
            return $this->apiResponse([
                'about_us' => $aboutUs
                    ? new AboutUsResource($aboutUs)
                    : [],
            ]);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }
    }


    public function allCurrency()
    {
        try {
            $currency = Currency::all();
            return $this->apiResponse([
                'currency' => CurrencyResource::collection($currency),
            ]);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }
    }

    public function allRegion(Request $request)
    {
        try {
            $query = Region::query();

            if ($request->has('search') && $request->filled('search')) {
                $search = $request->input('search');
                $query->where('name', 'like', "%$search%");
            }

            $regions = $query->get();

            return $this->apiResponse([
                'region' => RegionResource::collection($regions),
            ]);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }
    }



    public function allReviews()
    {
        try {
            $reviews = Review::orderBy('created_at', 'desc')->get();
            return $this->apiResponse([
                'reviews' => ReviewResource::collection($reviews),
            ]);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }
    }


    public function allBlogCategories(Request $request)
    {
        try {
            $categories = BlogCategory::query();

            if ($request->boolean('with_blogs')) {
                $categories->with('blogs');
            }

            return $this->apiResponse([
                'blog_categories' => BlogCategoryResource::collection($categories->get()),
            ]);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }
    }

    public function allBlogs(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $blogs = Blog::query();
            $request->validate([
                'blog_category_id' => 'nullable|string|exists:blog_categories,id',
            ]);
            if (isset($request->blog_category_id)) {
                $blogs->where('blog_category_id', $request->blog_category_id);
            }

            $allBlogs = $blogs->latest()->paginate($perPage);

            return $this->apiResponse([
                'blogs' => BlogResource::collection($allBlogs),
                'pagination' => [
                    'current_page' => $allBlogs->currentPage(),
                    'total' => $allBlogs->total(),
                    'per_page' => $allBlogs->perPage(),
                    'last_page' => $allBlogs->lastPage(),
                    'next_page_url' => $allBlogs->nextPageUrl(),
                    'prev_page_url' => $allBlogs->previousPageUrl(),
                ]
            ]);
        }
        catch
        (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function showBlog(Request $request)
    {
        try {
            $blog = Blog::findOrFail($request->blog_id);
            return $this->apiResponse([
                'blog' => new BlogResource($blog),
            ]);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }
    }
}
