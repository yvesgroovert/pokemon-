"use client";

import { useEffect, useState } from "react";

// Composant affichant une carte Pokémon
function PokemonCard({ pokemon }) {
  const { id, name, imageUrl, types } = pokemon;

  // Convertir types en tableau si c'est une string séparée par des virgules
  const typesArray = typeof types === "string" ? types.split(",") : types;

  return (
    <li key={id} className="mb-8 p-4 border rounded shadow-md max-w-md">
      <h2 className="text-2xl font-bold mb-2">
        {name} (ID: {id})
      </h2>

      {imageUrl ? (
        <img
          src={imageUrl}
          alt={name}
          className="w-40 h-40 object-contain mb-4"
          loading="lazy"
        />
      ) : (
        <div className="w-40 h-40 flex items-center justify-center bg-gray-200 mb-4">
          <span className="text-gray-500">Pas d'image</span>
        </div>
      )}

      <p>
        <strong>Type{typesArray.length > 1 ? "s" : ""} :</strong>{" "}
        {typesArray.length > 0 ? typesArray.join(", ") : "Inconnu"}
      </p>
    </li>
  );
}

export default function Pokepage() {
  const [pokemons, setPokemons] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetch("http://localhost:8000/sent/front")
      .then((res) => {
        if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
        return res.json();
      })
      .then((data) => {
        setPokemons(data);
        setLoading(false);
      })
      .catch((err) => {
        console.error("Erreur en récupérant les données:", err);
        setError(
          "Impossible de charger les données. Veuillez réessayer plus tard."
        );
        setLoading(false);
      });
  }, []);

  if (loading) return <p>Chargement des données...</p>;
  if (error) return <p className="text-red-600">{error}</p>;
  if (!pokemons.length) return <p>Aucun Pokémon trouvé.</p>;

  return (
    <main className="p-6 max-w-4xl mx-auto">
      <h1 className="text-4xl font-extrabold mb-6 text-center">
        Liste des Pokémons
      </h1>

      <ul className="grid grid-cols-1 sm:grid-cols-2 gap-6">
        {pokemons.map((pokemon) => (
          <PokemonCard key={pokemon.id} pokemon={pokemon} />
        ))}
      </ul>
    </main>
  );
}
