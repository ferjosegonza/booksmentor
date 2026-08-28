'use client'

import { useState, useEffect } from 'react'
import { useRouter } from 'next/navigation'
import Link from 'next/link'

interface BookResult {
  id: number | string
  type: 'local' | 'google'
  title: string
  author: string
  year?: number
  cover?: string
  isbn?: string
  googleBooksId?: string
  language?: string
}

export default function BookSearchPage() {
  const router = useRouter()
  const [query, setQuery] = useState('')
  const [results, setResults] = useState<BookResult[]>([])
  const [loading, setLoading] = useState(false)
  const [selectedBook, setSelectedBook] = useState<BookResult | null>(null)
  const [selectedLanguages, setSelectedLanguages] = useState<number[]>([])
  const [languages, setLanguages] = useState<any[]>([])
  const [subscribing, setSubscribing] = useState(false)

  useEffect(() => {
    fetchLanguages()
  }, [])

  const fetchLanguages = async () => {
    try {
      const token = localStorage.getItem('token')
      const response = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/api/subscriptions`, {
        headers: {
          'Authorization': `Bearer ${token}`,
        },
      })

      if (response.ok) {
        // For now, we'll use hardcoded languages
        setLanguages([
          { id: 1, nombre: 'Español', codigo: 'es' },
          { id: 2, nombre: 'Inglés', codigo: 'en' },
          { id: 3, nombre: 'Portugués', codigo: 'pt' },
          { id: 4, nombre: 'Italiano', codigo: 'it' },
          { id: 5, nombre: 'Francés', codigo: 'fr' },
        ])
      }
    } catch (error) {
      console.error('Error fetching languages:', error)
    }
  }

  const handleSearch = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!query.trim()) return

    setLoading(true)
    try {
      const token = localStorage.getItem('token')
      const response = await fetch(
        `${process.env.NEXT_PUBLIC_API_URL}/api/books/search?q=${encodeURIComponent(query)}`,
        {
          headers: {
            'Authorization': `Bearer ${token}`,
          },
        }
      )

      if (response.ok) {
        const data = await response.json()
        setResults(data)
      }
    } catch (error) {
      console.error('Error searching books:', error)
    } finally {
      setLoading(false)
    }
  }

  const handleSelectBook = (book: BookResult) => {
    setSelectedBook(book)
    setSelectedLanguages([])
  }

  const handleSubscribe = async () => {
    if (!selectedBook || selectedLanguages.length === 0) return

    setSubscribing(true)
    try {
      const token = localStorage.getItem('token')

      // If it's a Google book, create it first
      let bookId = selectedBook.id as number
      if (selectedBook.type === 'google') {
        const createResponse = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/api/books/create-from-google`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
          },
          body: JSON.stringify({ googleBooksId: selectedBook.googleBooksId }),
        })

        if (createResponse.ok) {
          const createdBook = await createResponse.json()
          bookId = createdBook.id
        } else {
          throw new Error('Error creating book')
        }
      }

      // Create subscription
      const subResponse = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/api/subscriptions`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`,
        },
        body: JSON.stringify({
          bookId,
          idiomaIds: selectedLanguages,
        }),
      })

      if (subResponse.ok) {
        router.push('/dashboard')
      } else {
        const data = await subResponse.json()
        alert(data.message || 'Error al suscribirse')
      }
    } catch (error) {
      console.error('Error subscribing:', error)
      alert('Error al suscribirse')
    } finally {
      setSubscribing(false)
    }
  }

  const toggleLanguage = (languageId: number) => {
    setSelectedLanguages(prev =>
      prev.includes(languageId)
        ? prev.filter(id => id !== languageId)
        : [...prev, languageId]
    )
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <nav className="bg-white shadow">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between h-16">
            <div className="flex">
              <div className="flex-shrink-0 flex items-center">
                <Link href="/dashboard" className="text-2xl font-bold text-primary-600">
                  BookMentor
                </Link>
              </div>
            </div>
            <div className="flex items-center">
              <Link
                href="/dashboard"
                className="text-sm text-gray-500 hover:text-gray-700"
              >
                Volver al Dashboard
              </Link>
            </div>
          </div>
        </div>
      </nav>

      <main className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div className="px-4 py-6 sm:px-0">
          <h1 className="text-3xl font-bold text-gray-900 mb-6">
            Buscar Libros
          </h1>

          <form onSubmit={handleSearch} className="mb-8">
            <div className="flex gap-4">
              <input
                type="text"
                value={query}
                onChange={(e) => setQuery(e.target.value)}
                placeholder="Escribe el nombre de un libro..."
                className="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
              />
              <button
                type="submit"
                disabled={loading}
                className="px-6 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 disabled:opacity-50"
              >
                {loading ? 'Buscando...' : 'Buscar'}
              </button>
            </div>
          </form>

          {!selectedBook ? (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {results.map((book) => (
                <div
                  key={`${book.type}-${book.id}`}
                  className="bg-white shadow rounded-lg overflow-hidden cursor-pointer hover:shadow-lg transition-shadow"
                  onClick={() => handleSelectBook(book)}
                >
                  {book.cover && (
                    <img
                      src={book.cover}
                      alt={book.title}
                      className="w-full h-48 object-cover"
                    />
                  )}
                  <div className="p-4">
                    <h3 className="text-lg font-semibold text-gray-900 mb-1">
                      {book.title}
                    </h3>
                    <p className="text-sm text-gray-600 mb-2">
                      {book.author}
                    </p>
                    {book.year && (
                      <p className="text-xs text-gray-500">
                        {book.year}
                      </p>
                    )}
                    <div className="mt-2 flex items-center">
                      <span className={`text-xs px-2 py-1 rounded-full ${
                        book.type === 'local' 
                          ? 'bg-green-100 text-green-800' 
                          : 'bg-blue-100 text-blue-800'
                      }`}>
                        {book.type === 'local' ? 'Disponible' : 'Nuevo'}
                      </span>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <div className="bg-white shadow rounded-lg p-6">
              <button
                onClick={() => setSelectedBook(null)}
                className="text-sm text-gray-500 hover:text-gray-700 mb-4"
              >
                ← Volver a resultados
              </button>

              <div className="flex gap-6 mb-6">
                {selectedBook.cover && (
                  <img
                    src={selectedBook.cover}
                    alt={selectedBook.title}
                    className="w-32 h-48 object-cover rounded"
                  />
                )}
                <div>
                  <h2 className="text-2xl font-bold text-gray-900 mb-2">
                    {selectedBook.title}
                  </h2>
                  <p className="text-lg text-gray-600 mb-2">
                    {selectedBook.author}
                  </p>
                  {selectedBook.year && (
                    <p className="text-sm text-gray-500">
                      Publicado: {selectedBook.year}
                    </p>
                  )}
                </div>
              </div>

              <div className="mb-6">
                <h3 className="text-lg font-semibold text-gray-900 mb-3">
                  Selecciona idiomas de traducción:
                </h3>
                <div className="flex flex-wrap gap-2">
                  {languages.map((lang) => (
                    <button
                      key={lang.id}
                      onClick={() => toggleLanguage(lang.id)}
                      className={`px-4 py-2 rounded-md border ${
                        selectedLanguages.includes(lang.id)
                          ? 'bg-primary-600 text-white border-primary-600'
                          : 'bg-white text-gray-700 border-gray-300 hover:border-primary-500'
                      }`}
                    >
                      {lang.nombre}
                    </button>
                  ))}
                </div>
              </div>

              <button
                onClick={handleSubscribe}
                disabled={subscribing || selectedLanguages.length === 0}
                className="w-full px-6 py-3 bg-primary-600 text-white rounded-md hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {subscribing ? 'Suscribiendo...' : 'Suscribirse'}
              </button>
            </div>
          )}
        </div>
      </main>
    </div>
  )
}
