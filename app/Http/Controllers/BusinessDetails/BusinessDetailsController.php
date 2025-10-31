<?php

namespace App\Http\Controllers\BusinessDetails;

use App\Http\Controllers\Controller;
use App\Models\BusinessDetails\BusinessDetailsModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BusinessDetailsController extends Controller
{
    // GET all
    public function GetAllClients()
    {
        $clients = BusinessDetailsModel::latest()->get();

        if ($clients->isNotEmpty()) {
            return response()->json([
                'message' => 'Clients found successfully',
                'clients' => $clients,
            ]);
        }

        return response()->json([
            'message' => 'No clients found',
            'clients' => [],
        ], 404);
    }

    // GET one
    public function show($id)
    {
        $client = BusinessDetailsModel::find($id);

        if (! $client) {
            return response()->json([
                'message' => 'Client not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Client found successfully',
            'data' => $client,
        ]);
    }

    // POST create
    public function create(Request $request)
    {
        // 1) validate the schema you are sending from Postman
        $validated = $request->validate([
            'data' => ['required', 'array'],

            // --- business_profile ---
            'data.business_profile.business_name' => ['required', 'string', 'max:255'],
            'data.business_profile.logo_or_brand_assets' => ['nullable', 'string', 'max:1000'],
            'data.business_profile.website_url' => ['nullable', 'url', 'max:255'],
            'data.business_profile.industry_or_niche' => ['nullable', 'string', 'max:255'],
            'data.business_profile.location' => ['nullable', 'string', 'max:255'],
            'data.business_profile.team_size' => ['nullable', 'string', 'max:50'],
            'data.business_profile.founded_year' => ['nullable', 'string', 'max:10'],

            // --- products_services ---
            'data.products_services.main_products_or_services' => ['nullable', 'array'],
            'data.products_services.business_description_one_line' => ['nullable', 'string', 'max:1000'],
            'data.products_services.unique_selling_point' => ['nullable', 'string', 'max:1000'],
            'data.products_services.target_audience_or_ideal_customer' => ['nullable', 'string', 'max:1000'],
            'data.products_services.key_problems_solved' => ['nullable', 'array'],
            'data.products_services.top_competitors' => ['nullable', 'array'],
            'data.products_services.business_goals_next_6_12_months' => ['nullable', 'array'],

            // --- contact_communication ---
            'data.contact_communication.contact_person_name' => ['required', 'string', 'max:255'],
            'data.contact_communication.role_or_position' => ['nullable', 'string', 'max:255'],
            'data.contact_communication.email_address' => ['required', 'email', 'max:255'],
            'data.contact_communication.phone_or_whatsapp' => ['nullable', 'string', 'max:50'],
            'data.contact_communication.social_media_links' => ['nullable', 'array'],
            'data.contact_communication.preferred_communication_method' => ['nullable', 'string', 'max:50'],
            'data.contact_communication.lead_source_or_referral' => ['nullable', 'string', 'max:255'],

            // --- sales_growth_information ---
            'data.sales_growth_information.ongoing_projects_or_active_deals' => ['nullable', 'array'],
            'data.sales_growth_information.worked_with_business_dev_partners_before' => ['nullable', 'string', 'max:1000'],
            'data.sales_growth_information.typical_client_or_deal_size' => ['nullable', 'string', 'max:255'],
            'data.sales_growth_information.existing_marketing_sales_materials' => ['nullable', 'array'],
            'data.sales_growth_information.biggest_challenges_getting_new_clients' => ['nullable', 'array'],
            'data.sales_growth_information.target_clients_or_industries' => ['nullable', 'array'],
            'data.sales_growth_information.current_business_stage' => ['nullable', 'string', 'max:50'],

            // --- brand_personality_preferences ---
            'data.brand_personality_preferences.brand_tone_or_style' => ['nullable', 'string', 'max:255'],
            'data.brand_personality_preferences.brand_keywords' => ['nullable', 'array'],
            'data.brand_personality_preferences.recent_news_awards_milestones' => ['nullable', 'string', 'max:2000'],
            'data.brand_personality_preferences.desired_partnerships_or_collaborations' => ['nullable', 'string', 'max:1000'],
            'data.brand_personality_preferences.focus_topics_or_trends' => ['nullable', 'array'],
            'data.brand_personality_preferences.expectations_from_ai_agent' => ['nullable', 'string', 'max:2000'],

            // --- ai_personalization ---
            'data.ai_personalization.auto_generate_personalized_pitches' => ['nullable', 'boolean'],
            'data.ai_personalization.analyze_competitors_and_suggest_opportunities' => ['nullable', 'boolean'],
            'data.ai_personalization.update_or_summary_frequency' => ['nullable', 'string', 'max:50'],
        ]);

        // 2) Backward compatibility: accept old keys and map -> new structure
        $data = $this->normalizeSchema($validated['data']);

        $client = BusinessDetailsModel::create([
            'data' => $data,
        ]);

        return response()->json([
            'message' => 'Client created successfully',
            'data' => $client,
        ], Response::HTTP_CREATED);
    }

    // PUT/PATCH update
    public function update(Request $request, $id)
    {
        $client = BusinessDetailsModel::find($id);

        if (! $client) {
            return response()->json([
                'message' => 'Client not found',
            ], 404);
        }

        // PATCH => partial update (merge into existing)
        if ($request->isMethod('patch')) {
            $validated = $request->validate([
                'data' => ['required', 'array'], // no required nested keys on PATCH
            ]);

            // Normalize incoming payload (handles old->new mapping)
            $incoming = $this->normalizeSchema($validated['data']);

            // Deep-merge incoming into existing JSON
            $current = $client->data ?? [];
            $merged = $this->deepMerge($current, $incoming);

            $client->update(['data' => $merged]);

            return response()->json([
                'message' => 'Client updated successfully (PATCH merge)',
                'data' => $client->fresh(),
            ]);
        }

        // PUT => full replace (require minimum guarantees)
        $validated = $request->validate([
            'data' => ['required', 'array'],

            // Minimum guarantees for a full payload
            'data.business_profile.business_name' => ['required', 'string', 'max:255'],
            'data.contact_communication.contact_person_name' => ['required', 'string', 'max:255'],
            'data.contact_communication.email_address' => ['required', 'email', 'max:255'],
        ]);

        $data = $this->normalizeSchema($validated['data']);
        $client->update(['data' => $data]);

        return response()->json([
            'message' => 'Client updated successfully (PUT replace)',
            'data' => $client->fresh(),
        ]);
    }

    /**
     * Deep merge two associative arrays (keeps arrays by replacing them entirely).
     */
    private function deepMerge(array $base, array $incoming): array
    {
        foreach ($incoming as $key => $value) {
            if (is_array($value) && isset($base[$key]) && is_array($base[$key])) {
                // If both sides are associative arrays, merge recursively.
                // If numeric arrays (lists), replace entirely (expected for lists like competitors, materials, etc.)
                $isAssoc = static function (array $a) {
                    return array_keys($a) !== range(0, count($a) - 1);
                };
                if ($isAssoc($base[$key]) && $isAssoc($value)) {
                    $base[$key] = $this->deepMerge($base[$key], $value);
                } else {
                    $base[$key] = $value; // replace lists
                }
            } else {
                $base[$key] = $value;
            }
        }

        return $base;
    }

    // DELETE destroy
    public function destroy($id)
    {
        $client = BusinessDetailsModel::find($id);

        if (! $client) {
            return response()->json([
                'message' => 'Client not found',
            ], 404);
        }

        $client->delete();

        return response()->json([
            'message' => 'Client deleted successfully',
        ]);
    }

    /**
     * Normalize/migrate incoming payloads to the new JSON schema.
     * Accepts your current schema and also maps old keys (contact.primary, products_services.core, etc.)
     */
    private function normalizeSchema(array $data): array
    {
        // If the new keys exist, keep them.
        $normalized = $data;

        // Map old -> new (only if new is missing)
        // Old: data.contact.primary.name/email/phone -> New: contact_communication.*
        if (! isset($normalized['contact_communication']) && isset($data['contact']['primary'])) {
            $normalized['contact_communication'] = [
                'contact_person_name' => $data['contact']['primary']['name'] ?? null,
                'role_or_position' => $data['contact']['primary']['role'] ?? null,
                'email_address' => $data['contact']['primary']['email'] ?? null,
                'phone_or_whatsapp' => $data['contact']['primary']['phone'] ?? null,
                'preferred_communication_method' => $data['contact']['primary']['preferred_channel'] ?? null,
                'social_media_links' => $data['contact']['primary']['socials'] ?? null,
                'lead_source_or_referral' => $data['contact']['lead_source'] ?? null,
            ];
        }

        // Old: products_services.core -> New: products_services.main_products_or_services
        if (isset($data['products_services']['core']) &&
            ! isset($normalized['products_services']['main_products_or_services'])) {
            $normalized['products_services']['main_products_or_services'] = $data['products_services']['core'];
        }

        // Old: strategy.goals -> New: products_services.business_goals_next_6_12_months
        if (isset($data['strategy']['goals']) &&
            ! isset($normalized['products_services']['business_goals_next_6_12_months'])) {
            $normalized['products_services']['business_goals_next_6_12_months'] = $data['strategy']['goals'];
        }

        // Old: preferences.tone -> New: brand_personality_preferences.brand_tone_or_style
        if (isset($data['preferences']['tone']) &&
            ! isset($normalized['brand_personality_preferences']['brand_tone_or_style'])) {
            $normalized['brand_personality_preferences']['brand_tone_or_style'] = $data['preferences']['tone'];
        }

        return $normalized;
    }
}
