import Link from 'next/link'

export default function Home() {
  return (
    <main className="min-h-screen bg-gradient-to-b from-blue-50 to-white">
      <div className="container mx-auto px-4 py-16">
        <div className="text-center">
          <h1 className="text-5xl font-bold text-gray-900 mb-6">
            BookMentor
          </h1>
          <p className="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
            Sistema de Envío de Enseñanzas por Suscripción
          </p>
          <p className="text-lg text-gray-500 mb-12 max-w-3xl mx-auto">
            Recibe enseñanzas extraídas de libros de forma personalizada y progresiva. 
            Suscríbete a tus libros favoritos y recibe una enseñanza por vez, traducida al idioma que elijas.
          </p>
          
          <div className="flex gap-4 justify-center">
            <Link 
              href="/auth/register"
              className="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium"
            >
              Comenzar Gratis
            </Link>
            <Link 
              href="/auth/login"
              className="px-6 py-3 border border-primary-600 text-primary-600 rounded-lg hover:bg-primary-50 transition-colors font-medium"
            >
              Iniciar Sesión
            </Link>
          </div>
        </div>

        <div className="mt-20 grid grid-cols-1 md:grid-cols-3 gap-8">
          <div className="bg-white p-6 rounded-lg shadow-md">
            <div className="text-3xl mb-4">📚</div>
            <h3 className="text-xl font-semibold mb-2">Catálogo de Libros</h3>
            <p className="text-gray-600">
              Busca y suscríbete a libros simplemente tipeando su nombre. El sistema los encuentra automáticamente.
            </p>
          </div>

          <div className="bg-white p-6 rounded-lg shadow-md">
            <div className="text-3xl mb-4">🌍</div>
            <h3 className="text-xl font-semibold mb-2">Múltiples Idiomas</h3>
            <p className="text-gray-600">
              Recibe las enseñanzas traducidas al idioma que prefieras. Español, inglés, portugués y más.
            </p>
          </div>

          <div className="bg-white p-6 rounded-lg shadow-md">
            <div className="text-3xl mb-4">⏰</div>
            <h3 className="text-xl font-semibold mb-2">Envíos Programados</h3>
            <p className="text-gray-600">
              Elige la frecuencia que prefieras: diaria, semanal o mensual. Las enseñanzas llegan automáticamente.
            </p>
          </div>
        </div>
      </div>
    </main>
  )
}
