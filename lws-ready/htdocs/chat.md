Requête POST JSON
      │
      ▼
[Validation CORS + méthode + body]
      │
      ▼
[normalizeText + tokenize]  ← "Quels additifs ?" → ["additif"]
      │
      ├── Étape 1 : FULLTEXT BOOLEAN → résultats ?
      │     │ OUI → candidates[]
      │     │ NON ↓
      └── Étape 2 : LIKE fallback (seuil 0.7)
            │
            ▼
      [Scoring ratio tokens matchés + bonus question]
            │
            ▼
      score ≥ seuil ? ──NON──► null ──► réponse "pas trouvé" + suggestions
            │
            OUI
            ▼
      [logQuestion + json_encode réponse trouvée]
