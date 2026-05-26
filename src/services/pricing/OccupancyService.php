<?php
/**
 * FIX: Delegates to the canonical version in services/OccupancyService.php.
 * PATTERN: Singleton (Database::getInstance()) is used inside OccupancyService.
 * PATTERN: Factory — ServiceFactory::make('occupancy') loads correctly.
 */
if (!class_exists('OccupancyService')) {
    require_once BASE_PATH . '/services/OccupancyService.php';
}
