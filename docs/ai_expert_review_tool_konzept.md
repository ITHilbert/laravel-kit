# Diskussion: KI-Kollaborations-Tool (Cross-LLM Loop)

## Ausgangslage
Die Zusammenarbeit zwischen Cursor (als ausführender Agent) und einem externen LLM (z.B. ChatGPT als Berater/Kritiker) über eine geteilte Diskussions-Datei liefert extrem hochwertige, Enterprise-taugliche Ergebnisse. 
Der manuelle Prozess ist aktuell: 
1. Agent generiert einen Vorschlag.
2. User fragt externes LLM nach "Second Opinion / Verbesserungen".
3. Externes LLM liefert architektonisches Feedback.
4. User reicht Feedback zurück an den Action-Agent.
5. Agent setzt um.

## Fragestellung
Soll/Kann dieser "Ping-Pong-Prozess" automatisiert oder toolgestützt abgebildet werden? Und wenn ja, in welcher Architektur (Eigenes Projekt, Package, Laravel-Kit Modul)?

## Lösungsansätze (Aktueller Stand)
- **Option A (Beschlossener Weg):** MCP Tool im `laravel-kit` (Vollautomatisch via API). Ideal als schnelle Lösung, da es manuelles Copy & Paste komplett eliminiert.
- **Option B (Verworfen):** Der manuelle Prompt-Workflow ist zu träge.
- **Option C (Zukunftsmusik):** Eigenes "AI Orchestrator" SaaS-Projekt. Sehr spannende Idee, wird aber aus Ressourcengründen für ein späteres Großprojekt aufgespart.

## Konzeption Option A (MCP Tool)
Das Tool fungiert als direkter Proxy zwischen mir (Cursor-Agent) und dem Ratgeber (OpenAI).
- **Abhängigkeiten:** Das Host-Projekt (`hetzner`) muss zwingend einen `OPENAI_API_KEY` in der `.env` hinterlegen. Wir nutzen Laravel's nativen HTTP-Client.
- **Konfigurierbarkeit:** Das zu nutzende Model wird in der `config/laravelkit.php` hinterlegt. Der Standard zielt auf das aktuell fähigste OpenAI Modell ab.
- **Enterprise System-Prompt:** Das Tool kapselt intern einen Experten-Prompt. Es zwingt das LLM zu Architekturüberprüfungen auf Enterprise-Niveau (Clean Code, Entkopplung, SOLID-Prinzipien).
- **Schnittstelle (Parameter):**
  - `content`: Mein geplanter Code oder das Diskussions-Protokoll, welches geprüft werden soll.

## Festgelegter Kollaborations-Workflow
Wenn dieses Tool im Einsatz ist, gilt zwischen uns folgender Prozess:
1. **Initiierung:** Du stellst eine Anforderung.
2. **Protokollierung:** Ich erstelle einen sauberen Lösungsentwurf als Diskussionspapier.
3. **Trigger:** Du befiehlst: *"Lass das prüfen!"*
4. **API-Request:** Ich sende das Dokument oder den Konzeptblock via MCP-Tool an OpenAI.
5. **Evaluierung & Auto-Merge:** Ich lese das ChatGPT-Feedback. Offensichtliche, gute Architektur-Verbesserungen arbeite ich direkt in unser Konzept ein.
6. **Entscheidungsvorlage:** Bei abweichenden Meinungen oder fundamentalen Richtungswechseln durch ChatGPT, fasse ich diese übersichtlich zusammen und lege sie dir zur Entscheidung / Veto vor.

