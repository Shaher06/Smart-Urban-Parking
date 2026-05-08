<?php
/**
 * BUG FIX: This file previously re-declared class OccupancyService,
 * causing a fatal PHP error: "Cannot declare class OccupancyService,
 * because the name is already in use" when both files were loaded
 * in the same request via ServiceFactory or require_once chains.
 *
 * FIX: Delegates to the canonical version in services/OccupancyService.php.
 * PATTERN: Singleton (Database::getInstance()) is used inside OccupancyService.
 * PATTERN: Factory — ServiceFactory::make('occupancy') loads correctly.
 */
if (!class_exists('OccupancyService')) {
    require_once BASE_PATH . '/services/OccupancyService.php';
}
