<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class EventController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/events",
     *     summary="List all events for the authenticated user",
     *     tags={"Events"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index()
    {
        return \App\Models\Event::where('user_id', auth()->id())->get();
    }

    /**
     * @OA\Post(
     *     path="/api/events",
     *     summary="Create a new event",
     *     tags={"Events"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","start_time","end_time"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="start_time", type="string", format="date-time"),
     *             @OA\Property(property="end_time", type="string", format="date-time"),
     *             @OA\Property(property="color", type="string"),
     *             @OA\Property(property="customer_name", type="string"),
     *             @OA\Property(property="customer_phone", type="string"),
     *             @OA\Property(property="payment_status", type="string"),
     *             @OA\Property(property="event_type", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Event created"),
     *     @OA\Response(response=422, description="Validation error or time conflict")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'color' => 'nullable|string',
            'customer_name' => 'nullable|string',
            'customer_phone' => 'nullable|string',
            'payment_status' => 'nullable|string',
            'event_type' => 'nullable|string|in:social_event,photo_shoot,venue_visit',
        ]);

        // Check for conflicting events
        $conflict = \App\Models\Event::where('user_id', auth()->id())
            ->where('start_time', $validated['start_time'])->exists();
        if ($conflict) {
            return response()->json(['message' => 'Já existe um agendamento para este horário.'], 422);
        }

        $validated['user_id'] = auth()->id();
        return \App\Models\Event::create($validated);
    }

    /**
     * @OA\Get(
     *     path="/api/events/{event}",
     *     summary="Get a specific event",
     *     tags={"Events"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="event", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(\App\Models\Event $event)
    {
        if ($event->user_id !== auth()->id()) {
            abort(403, 'Acesso negado');
        }
        return $event;
    }

    /**
     * @OA\Put(
     *     path="/api/events/{event}",
     *     summary="Update an existing event",
     *     tags={"Events"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="event", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="start_time", type="string", format="date-time"),
     *             @OA\Property(property="end_time", type="string", format="date-time"),
     *             @OA\Property(property="color", type="string"),
     *             @OA\Property(property="customer_name", type="string"),
     *             @OA\Property(property="customer_phone", type="string"),
     *             @OA\Property(property="payment_status", type="string"),
     *             @OA\Property(property="event_type", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Event updated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Validation error or time conflict")
     * )
     */
    public function update(Request $request, \App\Models\Event $event)
    {
        if ($event->user_id !== auth()->id()) {
            abort(403, 'Acesso negado');
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string',
            'description' => 'nullable|string',
            'start_time' => 'sometimes|required|date',
            'end_time' => 'sometimes|required|date|after:start_time',
            'color' => 'nullable|string',
            'customer_name' => 'nullable|string',
            'customer_phone' => 'nullable|string',
            'payment_status' => 'nullable|string',
            'event_type' => 'nullable|string|in:social_event,photo_shoot,venue_visit',
        ]);

        if (isset($validated['start_time'])) {
            $conflict = \App\Models\Event::where('user_id', auth()->id())
                ->where('start_time', $validated['start_time'])
                ->where('id', '!=', $event->id)
                ->exists();
            if ($conflict) {
                return response()->json(['message' => 'Já existe um agendamento para este horário.'], 422);
            }
        }

        $event->update($validated);

        return $event;
    }

    /**
     * @OA\Delete(
     *     path="/api/events/{event}",
     *     summary="Delete an event",
     *     tags={"Events"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="event", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Event deleted"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function destroy(\App\Models\Event $event)
    {
        if ($event->user_id !== auth()->id()) {
            abort(403, 'Acesso negado');
        }

        $event->delete();

        return response()->json(['message' => 'Event deleted']);
    }
}
