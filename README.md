# Work Task Resolution Report

## Overview

This is a small Laravel API that returns a report of work tasks grouped by resolution type within a given date range.

The goal was to keep things simple, readable, and close to real-world backend practices without over-engineering.

---

## Endpoint

GET /api/reports/work-tasks/resolutions?from=YYYY-MM-DD&to=YYYY-MM-DD

### Example

/api/reports/work-tasks/resolutions?from=2024-01-01&to=2024-12-31

---

## What it does

- Filters work tasks based on `created_at` between `from` and `to`
- Excludes:
  - Calls in `Draft`
  - Calls in `Archived`
- Ignores work tasks with no resolution type
- Groups results by resolution type
- Returns count per resolution

---

## Response Example

```json
{
  "resolution_types": [
    {
      "id": 1,
      "name": "Fix Complete – Parts Collection Required",
      "description": "Parts collection needed",
      "count": 3
    }
  ]
}