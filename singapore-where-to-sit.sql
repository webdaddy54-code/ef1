-- Singapore GP — Where to Sit guide (Marina Bay Street Circuit)
-- Generated 2026-09-11 — portable MySQL/SQLite
-- Run in Cloudways Database Manager against the live database.

-- 1) Seating guide row
INSERT INTO seating_guides
    (race_id, slug, page_title, meta_description, meta_keywords, hero_subtitle,
     intro_html, ga_section_html, new_for_year_html, practical_tips_html, disclaimer_html,
     circuit_stats_grandstands, circuit_stats_corners, circuit_stats_length, status)
VALUES (
    (SELECT race_id FROM races WHERE slug = 'singapore-grand-prix'),
    'where-to-sit-at-singapore',
    'Where to Sit at Marina Bay | 2026 Singapore Grand Prix Grandstand Guide',
    'The definitive guide to the best grandstands and viewing spots at Marina Bay for the 2026 Singapore Grand Prix. Ranked recommendations for every budget, from Walkabout to the Pit Grandstand.',
    'where to sit singapore grand prix, marina bay grandstands, singapore gp seating guide, pit grandstand, connaught grandstand, padang grandstand, F1 night race tickets',
    'Your Complete Grandstand Guide | 2026 Singapore Grand Prix',
    '<p class="lead">Choosing the right grandstand at Marina Bay is different from any other race on the calendar. This is Formula 1''s original night race — a 19-turn street circuit threading through downtown Singapore under floodlights, with grandstands squeezed between skyscrapers, the waterfront and some of the best off-track entertainment of the season.</p>
<p>This guide ranks the best places to watch the 2026 Singapore Grand Prix based on viewing angle, overtaking potential, atmosphere, shelter from the tropical weather and value for money. Whether you want the heavy-braking drama of Turn 14, the start-line theatre of the Pit Straight, or the postcard views along the Bay, there''s a seat for every type of fan.</p>
<p><strong>New for 2026:</strong> Formula 1''s new-generation cars make their first appearance at Marina Bay — smaller, lighter and with active aerodynamics, they should be able to follow each other far more closely through the tight street sections. Expect the action into Turns 1, 7 and 14 to be the best the circuit has seen in years.</p>',
    '<p>At Marina Bay, General Admission comes in the form of <strong>Walkabout</strong> tickets. A standard Walkabout pass gives you roaming access to Zone 4 — the area around the Padang, Esplanade and the concert stages — where you can find your own spot along the fencing. <strong>Premier Walkabout</strong> upgrades you to all four zones, opening up standing areas around Turns 1, 7 and 14 as well. For a street circuit, the roaming freedom is excellent value.</p>
<h5 class="text-f1 mt-4">Best Walkabout Viewing Spots</h5>
<p><strong>Esplanade Waterfront (Zone 4)</strong> — Standing room along the water with the cars sweeping past the bay and the city skyline behind. The classic Singapore backdrop, and the best atmosphere once the concerts start.</p>
<p><strong>Turn 1 (Zone 1, Premier Walkabout)</strong> — A raised bank near the first corner where you can watch the lap-one melee and DRS moves without a reserved seat. Get there early on race day; it fills fast.</p>
<p><strong>Connaught (Zone 1, Premier Walkabout)</strong> — Limited standing room near the Turn 14 braking zone. The best overtaking on the circuit, free with a Premier Walkabout pass if you''re prepared to stake out a spot.</p>
<p>Tip: fence spots go hours before the race. Bring a camping stool for the support sessions, then move to the fence when the F1 cars roll out.</p>',
    '<div class="col-md-4 mb-3">
    <div class="card h-100">
        <div class="card-body">
            <h5 class="card-title text-f1"><i class="bi bi-lightning-charge"></i> New-Generation Cars</h5>
            <p>F1''s smaller, lighter 2026 cars debut at Marina Bay with active aerodynamics and a shorter wheelbase. They should follow far more closely through the tight street sections — expect more overtaking into Turns 1, 7 and 14 than recent years.</p>
        </div>
    </div>
</div>
<div class="col-md-4 mb-3">
    <div class="card h-100">
        <div class="card-body">
            <h5 class="card-title text-f1"><i class="bi bi-signpost-2"></i> 19 Turns, Flat Out</h5>
            <p>The shorter 4.94 km layout introduced in 2023 continues, packing the action into a tighter arena. The Bayfront straight between Turns 15 and 16 is now one of the fastest parts of the lap — and the new cars will be seriously quick through it.</p>
        </div>
    </div>
</div>
<div class="col-md-4 mb-3">
    <div class="card h-100">
        <div class="card-body">
            <h5 class="card-title text-f1"><i class="bi bi-music-note-beamed"></i> Concert Headliners</h5>
            <p>The post-race concerts at the Padang and Wharf stages are included with every ticket. Headliners are announced through the summer — past years have featured global acts. Check the official Singapore GP website for the 2026 line-up.</p>
        </div>
    </div>
</div>',
    '<h5 class="text-f1">Getting There</h5>
<p>Marina Bay sits in the heart of the city, and the MRT is by far the easiest way in. <strong>Bayfront</strong> (Circle/Downtown Line) serves the Bay and Pit areas; <strong>Promenade</strong> and <strong>Esplanade</strong> (Circle Line) are closest for the Padang and Stamford grandstands; <strong>City Hall</strong> (North-South/East-West Lines) covers the Zone 4 gates. Roads around the circuit close from early afternoon on race days, so taxis drop at designated points outside the park — allow time for the walk to your gate.</p>
<h5 class="text-f1 mt-4">What to Bring</h5>
<p>A rain poncho (tropical downpours arrive without warning — the 2022 race was delayed over an hour), ear protection, a refillable water bottle (free hydration stations inside), a portable phone charger, and light clothing. Even at night it''s 28–30°C with heavy humidity. Covered seating is essentially limited to the Pit Grandstand and hospitality decks, so assume you''re exposed to the weather wherever you sit.</p>
<h5 class="text-f1 mt-4">Race Weekend Timing</h5>
<p>Everything runs late in Singapore — the F1 sessions are in the evening to match the European TV audience, with support races from late afternoon. The headline concerts follow the on-track action, so plan to stay late at least one night of the weekend.</p>',
    '<p class="small text-muted mb-0"><strong>Disclaimer:</strong> This guide reflects our independent opinions based on years of following and attending the Singapore Grand Prix. Grandstand names, layouts, pricing and availability are subject to change by the race promoter. Always check the official Singapore Grand Prix website for the latest information before booking.</p>',
    '10+',
    '19',
    '4.94 km',
    'published'
);

-- 2) Grandstand rows (ranked 1-6)
INSERT INTO grandstands
    (guide_id, name, rank_position, subtitle, description_html, best_for,
     overtaking_rating, badge_text, badge_colour, is_covered, is_new, display_order)
VALUES (
    (SELECT guide_id FROM seating_guides WHERE slug = 'where-to-sit-at-singapore'),
    'Pit Grandstand', 1, 'Grid, pit stops, podium and fireworks — the full ceremony under the lights',
    '<p>The Pit Grandstand puts you at the heart of the Singapore Grand Prix, directly opposite the team garages on the start/finish straight. This is where the grid forms under the floodlights, where the five red lights go out, and where the pit crews perform their stops directly in front of you. After the chequered flag, the podium ceremony and the fireworks display unfold right above this stand.</p>
<p>It''s also one of the very few covered grandstands at Marina Bay — worth its weight in gold when a tropical downpour rolls in, as it did when the 2022 race was delayed over an hour. Overtaking on the straight itself is rare, but you''ll see every start, every restart and every pit stop, and the atmosphere at lights-out is electric. The Padang concert stage is a short walk away for the post-race gigs. Premium-priced and always the first stand to sell out.</p>',
    'Race start, pit stop action, podium ceremony, concerts, wet-weather cover.', 2, 'Best Overall Experience', 'success', 1, 0, 1
);

INSERT INTO grandstands
    (guide_id, name, rank_position, subtitle, description_html, best_for,
     overtaking_rating, badge_text, badge_colour, is_covered, is_new, display_order)
VALUES (
    (SELECT guide_id FROM seating_guides WHERE slug = 'where-to-sit-at-singapore'),
    'Connaught Grandstand', 2, 'The heaviest braking zone on the calendar — overtaking guaranteed',
    '<p>Turn 14 at Connaught is the single best overtaking spot at Marina Bay. Cars arrive at close to 300 km/h along Raffles Boulevard before stamping on the brakes for a tight right-hander taken at barely 90 km/h — the heaviest braking zone on the 2026 calendar. Drivers lunge down the inside here lap after lap, and lock-ups, run-wide moments and the occasional bit of contact are all part of the menu.</p>
<p>The grandstand sits close to the track with a clear view of the entire braking zone and the corner exit, where traction is everything and mistakes are punished instantly. Because this is one of the last big braking zones before the run to the flag, moves made here tend to stick. If your priority is wheel-to-wheel racing rather than ceremony, this is the stand to book.</p>',
    'Overtaking action, heavy braking duels, close-up racecraft.', 5, 'Best for Overtaking', 'success', 0, 0, 2
);

INSERT INTO grandstands
    (guide_id, name, rank_position, subtitle, description_html, best_for,
     overtaking_rating, badge_text, badge_colour, is_covered, is_new, display_order)
VALUES (
    (SELECT guide_id FROM seating_guides WHERE slug = 'where-to-sit-at-singapore'),
    'Turn 1 Grandstand', 3, 'Lap-one chaos and DRS moves into the opening corner',
    '<p>The Turn 1 Grandstand overlooks the opening corner of the lap — a 90-degree left-hander that funnels the entire field from over 280 km/h down to second gear at the start. Lap one here is drama more often than not: the rain-soaked 2022 start sent cars skating in every direction, and even in the dry the charge to the first corner at Marina Bay produces contact and position swaps.</p>
<p>Beyond the start, Turn 1 remains one of the circuit''s genuine overtaking opportunities thanks to the DRS zone on the approach. You''ll see cars defending and attacking into the corner, plus the run through Turns 2 and 3 as the field strings out. An excellent choice if you want start-line drama without paying Pit Grandstand prices.</p>',
    'Race starts, first-lap drama, DRS overtaking.', 4, 'Turn 1 Drama', 'danger', 0, 0, 3
);

INSERT INTO grandstands
    (guide_id, name, rank_position, subtitle, description_html, best_for,
     overtaking_rating, badge_text, badge_colour, is_covered, is_new, display_order)
VALUES (
    (SELECT guide_id FROM seating_guides WHERE slug = 'where-to-sit-at-singapore'),
    'Padang Grandstand', 4, 'The postcard seat — historic skyline, concerts and festival atmosphere',
    '<p>The Padang Grandstand sits opposite the historic Padang field, framed by the National Gallery and the colonial skyline — the most photographed backdrop at Marina Bay. On track, you watch the cars thread through Turns 9 and 10, a tricky left-right flick past the old Supreme Court where precision matters more than bravery. Overtaking is rare here, but the setting is unmatched anywhere in Formula 1.</p>
<p>The real draw is everything around the racing: the main concert stage is right next door, the biggest food and drink villages are in this zone, and the atmosphere after the race — when the headline acts come on — is a festival in its own right. If your weekend is as much about the party as the racing, this is your stand.</p>',
    'Atmosphere, concerts, iconic backdrop, food and drink.', 2, 'Best Atmosphere', 'info', 0, 0, 4
);

INSERT INTO grandstands
    (guide_id, name, rank_position, subtitle, description_html, best_for,
     overtaking_rating, badge_text, badge_colour, is_covered, is_new, display_order)
VALUES (
    (SELECT guide_id FROM seating_guides WHERE slug = 'where-to-sit-at-singapore'),
    'Stamford Grandstand', 5, '300 km/h to a standstill at Memorial Corner — action at a sensible price',
    '<p>The Stamford Grandstand overlooks Turn 7 — the Memorial Corner — where cars arrive at around 300 km/h down the flat-out Nicoll Highway before braking hard into a 90-degree left. Along with Turn 14, this is one of the two genuine overtaking zones at Marina Bay, and it regularly produces dive-bombs, defensive squeezes and the occasional trip down the escape road.</p>
<p>Stamford is typically priced below the Pit and Padang grandstands while still delivering real on-track action, making it one of the best value reserved seats on the circuit. You''ll also see the cars accelerating away through Turn 8, and big screens opposite keep you across the rest of the race.</p>',
    'Overtaking, value for money, straight-line speed.', 4, 'Great Value', 'info', 0, 0, 5
);

INSERT INTO grandstands
    (guide_id, name, rank_position, subtitle, description_html, best_for,
     overtaking_rating, badge_text, badge_colour, is_covered, is_new, display_order)
VALUES (
    (SELECT guide_id FROM seating_guides WHERE slug = 'where-to-sit-at-singapore'),
    'Bay Grandstand', 6, 'Cars at full stretch along the waterfront — the most spectacular view in F1',
    '<p>The Bay Grandstand lines the waterfront section introduced when the circuit layout changed in 2023. Cars sweep along the bay at high speed with the water on one side and the glittering Marina Bay Sands skyline on the other — at night, under floodlights, it''s the most spectacular visual in Formula 1. If you''re coming home with photographs, this is where they''ll be taken.</p>
<p>The trade-off is the action: this is a fast, flowing section where overtaking is opportunistic rather than guaranteed. You''ll see the cars at full stretch, sparks flying over the bumps, but fewer wheel-to-wheel moments than at Turn 1 or Connaught. Come for the views and the atmosphere — and pair it with a Walkabout day if you want the racing action too.</p>',
    'Photography, skyline views, night-race spectacle.', 3, 'Iconic Views', 'warning', 0, 0, 6
);

-- 3) Verification — expect grandstand_count = 6
SELECT g.page_title, COUNT(gs.grandstand_id) AS grandstand_count
FROM seating_guides g
LEFT JOIN grandstands gs ON gs.guide_id = g.guide_id
WHERE g.slug = 'where-to-sit-at-singapore'
GROUP BY g.guide_id;
