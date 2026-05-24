<?php

namespace App\Support;

/**
 * Sparkline / chart series for the dashboard.
 * Mirrors the prototype's data.js arrays so screens look identical out of the box.
 * Once real reporting exists, swap these for live queries.
 */
class Charts
{
    public const ORDERS_14D     = [62, 71, 58, 84, 89, 76, 92, 88, 95, 81, 88, 102, 96, 142];
    public const REVENUE_14D    = [2.1, 2.4, 2.0, 2.7, 2.9, 2.6, 3.1, 3.0, 3.2, 2.8, 3.0, 3.5, 3.3, 3.84];
    public const TURNAROUND_14D = [16, 17, 18, 15, 14, 16, 15, 14, 13, 15, 14, 13, 14, 14];
    public const QUEUE_14D      = [12, 13, 14, 16, 14, 15, 17, 18, 15, 16, 17, 16, 17, 18];
    public const TUNERS_14D     = [5, 6, 6, 5, 6, 6, 6, 7, 6, 6, 6, 6, 6, 6];
    public const DISPUTES_14D   = [2, 2, 3, 2, 2, 3, 3, 2, 2, 2, 3, 3, 3, 3];

    public const STOCK_CURVE = [80, 95, 115, 140, 170, 195, 215, 225, 228, 225, 218, 210];
    public const TUNED_CURVE = [85, 108, 138, 178, 220, 255, 272, 278, 280, 275, 268, 258];
}
