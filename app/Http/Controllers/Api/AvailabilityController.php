<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    /**
     * Business hours: start and end (24h format).
     */
    private int $startHour = 8;
    private int $endHour   = 18;

    /**
     * Default slot duration in minutes.
     */
    private int $slotMinutes = 60;

    /**
     * @OA\Get(
     *     path="/api/availability/next-days",
     *     summary="Retorna os 3 próximos dias com horários livres a partir de hoje",
     *     tags={"Disponibilidade"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista dos 3 próximos dias com horários livres",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="date", type="string", example="2026-03-04"),
     *                 @OA\Property(
     *                     property="free_slots",
     *                     type="array",
     *                     @OA\Items(type="string", example="08:00")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function nextDays()
    {
        $results = [];
        $current = Carbon::today();

        while (count($results) < 3) {
            $freeSlots = $this->getFreeSlots($current);

            if (count($freeSlots) > 0) {
                $results[] = [
                    'date'       => $current->toDateString(),
                    'free_slots' => $freeSlots,
                ];
            }

            $current->addDay();
        }

        return response()->json($results);
    }

    /**
     * @OA\Get(
     *     path="/api/availability/{date}",
     *     summary="Retorna os horários livres para um dia específico",
     *     tags={"Disponibilidade"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="date",
     *         in="path",
     *         required=true,
     *         description="Data no formato YYYY-MM-DD",
     *         @OA\Schema(type="string", format="date", example="2026-03-05")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de horários livres para o dia informado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="date", type="string", example="2026-03-05"),
     *             @OA\Property(
     *                 property="free_slots",
     *                 type="array",
     *                 @OA\Items(type="string", example="09:00")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Data inválida"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function freeSlotsByDate(string $date)
    {
        try {
            $day = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Data inválida. Use o formato YYYY-MM-DD.'], 400);
        }

        $freeSlots = $this->getFreeSlots($day);

        return response()->json([
            'date'       => $day->toDateString(),
            'free_slots' => $freeSlots,
        ]);
    }

    /**
     * Returns free time slots for a given day, comparing against
     * the authenticated user's events.
     */
    private function getFreeSlots(Carbon $day): array
    {
        $userId = auth()->id();

        // Fetch all events that overlap with this day for the user
        $events = Event::where('user_id', $userId)
            ->whereDate('start_time', $day->toDateString())
            ->get(['start_time', 'end_time']);

        $busySlots = $events->map(fn($e) => Carbon::parse($e->start_time)->format('H:i'))->toArray();

        $freeSlots = [];
        $slot = $day->copy()->setTime($this->startHour, 0);
        $end  = $day->copy()->setTime($this->endHour, 0);

        while ($slot->lt($end)) {
            $slotLabel = $slot->format('H:i');

            if (!in_array($slotLabel, $busySlots)) {
                $freeSlots[] = $slotLabel;
            }

            $slot->addMinutes($this->slotMinutes);
        }

        return $freeSlots;
    }
}
