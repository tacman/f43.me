# Reuse and AI opportunities

Reviewed 2026-09-29. These are recommendations, not an implemented AI integration.

## What to reuse from f43

`src/Content/Extractor.php` implements an ordered chain: source-specific URL
improver, API/media extractor, Graby parser, parser fallback, RSS body fallback,
then HTML converters. `src/Content/Import.php` deduplicates permalinks and stores
extracted content. Feed fetches can run through Messenger with locks and retries.
This is useful upstream in a news-ingestion service or Harvest, particularly for
RSS entries that only contain excerpts. Keep extraction server-side.

For enrichment, persist/flush an article before dispatching work by item ID.
The current `ItemsCachedEvent` runs after each feed has been flushed, but carries
feed slugs rather than new article IDs. Use an explicit persisted-item event for
enrichment; if introducing an outer transaction, dispatch after commit or use a
transactional outbox. Key results by source content
hash, model, prompt version and schema version so retries do not create duplicate
paid calls. Store summaries and extracted metadata separately from original text.

## Ink

Ink already has Harvest folios, segments and an ArticleDocument search model with
`denseSummary` and OCR text. Its PLAN calls for source-revision-keyed derivatives
and article-scoped AI that cites segments. Reuse extraction adapters for digital
sources; consume Harvest segments for OCR material. Preserve paragraph positions
and reader links. Feed AI summaries through the existing mapper/indexer rather
than introducing a second search path. Evaluate keyword search before adding RAG.
Digital bodies in `extras.bodyText` need explicit export; summaries cannot replace
article bodies.

## Tobacco / zm

The live site responds as Tobacco News Archive. Its TobaccoDocument already indexes
public label, `searchSummary`, subjects and year, explicitly excluding preserved
full article bodies. Its claim history has source, confidence, agent and timestamp;
claims.jsonl/mediary remains authoritative. Emit model-derived summaries, subjects,
organizations, places and event dates as provenance-bearing claims upstream, then
project them into the existing search fields. Keep source excerpts/segment references
with claims for editorial verification. Use f43 feed ingestion only if adding a new
incoming-news pipeline; the existing archive does not need another RSS application.

## First AI experiment

Evaluate summary + subject/entity extraction on a small, manually reviewed corpus.
Use one typed result schema across local and hosted providers and measure factual
support, omissions, latency and cost. A local Ollama model avoids per-request API
fees (hardware/runtime costs remain); a hosted model can be compared on the same
samples. No paid calls or model downloads were performed during installation.

Symfony AI offers provider abstraction and structured output:
https://symfony.com/doc/current/ai/components/platform.html

Ollama supports JSON-schema structured output locally (its current cloud service
has different support):
https://docs.ollama.com/capabilities/structured-outputs

Treat article content as untrusted data, validate returned fields, and keep AI
failures independent of successful article ingestion. Start with summaries and
classification; build source-cited retrieval only after the content/index pipeline
is reliable.
