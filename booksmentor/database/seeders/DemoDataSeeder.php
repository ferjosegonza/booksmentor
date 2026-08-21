<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Usuario;
use App\Models\Libro;
use App\Models\Ensenanza;
use App\Models\Traduccion;
use App\Models\Suscripcion;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Admin User
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@booksmentor.com'],
            [
                'name' => 'Administrador BooksMentor',
                'password' => Hash::make('admin12345'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $adminUsuario = Usuario::firstOrCreate(
            ['email' => 'admin@booksmentor.com'],
            [
                'user_id' => $adminUser->id,
                'nombre' => 'Administrador BooksMentor',
                'frecuencia_id' => 1, // Diaria
                'plan_id' => 4, // Premium
                'hora_envio' => '08:00:00',
                'zona_horaria' => 'America/Argentina/Buenos_Aires',
                'activo' => true,
            ]
        );

        // 2. Create Standard Demo User
        $clientUser = User::firstOrCreate(
            ['email' => 'usuario@booksmentor.com'],
            [
                'name' => 'Juan Pérez',
                'password' => Hash::make('usuario12345'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        $clientUsuario = Usuario::firstOrCreate(
            ['email' => 'usuario@booksmentor.com'],
            [
                'user_id' => $clientUser->id,
                'nombre' => 'Juan Pérez',
                'frecuencia_id' => 1, // Diaria
                'plan_id' => 3, // Pro
                'hora_envio' => '07:30:00',
                'zona_horaria' => 'America/Argentina/Buenos_Aires',
                'activo' => true,
            ]
        );

        // 3. Create Sample Books with Teachings and Translations
        $booksData = [
            [
                'titulo' => 'Hábitos Atómicos',
                'autor' => 'James Clear',
                'descripcion' => 'Un marco probado para mejorar cada día un 1%. Cambios pequeños que producen resultados extraordinarios.',
                'portada_url' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600&auto=format&fit=crop&q=80',
                'idioma_original_id' => 1, // Español
                'anio_publicacion' => 2018,
                'tags' => [1, 2], // Productividad, Hábitos
                'ensenanzas' => [
                    [
                        'orden' => 1,
                        'tema' => 'El poder del 1%',
                        'texto' => 'No te concentres en metas grandes; concéntrate en mejorar un 1% cada día. Las mejoras marginales se acumulan exponencialmente a lo largo del tiempo.',
                        'traducciones' => [
                            2 => 'Do not focus on massive goals; focus on improving by 1% each day. Marginal gains compound exponentially over time.', // EN
                            3 => 'Não se concentre em grandes objetivos; concentre-se em melhorar 1% a cada dia. Os ganhos marginais acumulam-se exponencialmente com o tempo.', // PT
                            4 => 'Non concentrarti su grandi obiettivi; concentrati sul migliorare dell\'1% ogni giorno. I miglioramenti marginali si accumulano esponenzialmente nel tempo.', // IT
                            5 => 'Ne vous concentrez pas sur de grands objectifs ; concentrez-vous sur une amélioration de 1 % chaque jour. Les gains marginaux se cumulent de manière exponentielle avec le temps.', // FR
                            7 => '不要只专注于宏伟的目标，每天进步1%即可。微小的改进会随着时间呈指数级累积。', // ZH
                            8 => '不要只專注於宏偉的目標，每天進步1%即可。微小的改進會隨著時間呈指數級累積。', // ZH-TW
                        ]
                    ],
                    [
                        'orden' => 2,
                        'tema' => 'La regla de los 2 minutos',
                        'texto' => 'Cuando comiences un nuevo hábito, debe tomarte menos de dos minutos hacerlo. Facilita el inicio para vencer la inercia y construir consistencia.',
                        'traducciones' => [
                            2 => 'When starting a new habit, it should take less than two minutes to do. Make starting easy to defeat friction and build consistency.',
                            3 => 'Ao iniciar um novo hábito, deve levar menos de dois minutos para fazê-lo. Facilite o início para vencer a inércia e construir consistência.',
                            4 => 'Quando inizi una nuova abitudine, dovrebbe richiedere meno di due minuti. Rendi facile l\'inizio per superare l\'inerzia e costruire costanza.',
                            5 => 'Lorsque vous commencez une nouvelle habitude, cela devrait prendre moins de deux minutes. Facilitez le démarrage pour vaincre l\'inertie et instaurer la régularité.',
                            7 => '当你开始一个新习惯时，应该在两分钟内完成。让开始变得简单，以克服惯性并建立持久的一致性。',
                            8 => '當你開始一個新習慣時，應該在兩分鐘內完成。讓開始變得簡單，以克服慣性並建立持久的一致性。',
                        ]
                    ],
                    [
                        'orden' => 3,
                        'tema' => 'Diseño del entorno',
                        'texto' => 'El entorno es la mano invisible que moldea el comportamiento humano. Haz que las señales de los buenos hábitos sean obvias y las de los malos hábitos sean invisibles.',
                        'traducciones' => [
                            2 => 'Environment is the invisible hand that shapes human behavior. Make the cues of good habits obvious and those of bad habits invisible.',
                            3 => 'O ambiente é a mão invisível que molda o comportamento humano. Torne as pistas de bons hábitos óbvias e as de maus hábitos invisíveis.',
                            4 => 'L\'ambiente è la mano invisibile che modella il comportamento umano. Rendi evidenti i segnali delle buone abitudini e invisibili quelli delle cattive.',
                            5 => 'L\'environnement est la main invisible qui façonne le comportement humain. Rendez les indices des bonnes habitudes évidents et ceux des mauvaises habitudes invisibles.',
                            7 => '环境是塑造人类行为的无形之手。让好习惯的提示显而易见，让坏习惯的诱因无影无踪。',
                            8 => '環境是塑造人類行為的無形之手。讓好習慣的提示顯而易見，讓壞習慣的誘因無影無蹤。',
                        ]
                    ]
                ]
            ],
            [
                'titulo' => 'El Hombre en Busca de Sentido',
                'autor' => 'Viktor Frankl',
                'descripcion' => 'Una lección eterna sobre la resiliencia humana, la dignidad y el propósito supremo en medio de la adversidad.',
                'portada_url' => 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=600&auto=format&fit=crop&q=80',
                'idioma_original_id' => 1,
                'anio_publicacion' => 1946,
                'tags' => [5, 6], // Psicología, Filosofía
                'ensenanzas' => [
                    [
                        'orden' => 1,
                        'tema' => 'La última libertad humana',
                        'texto' => 'A un hombre se le puede arrebatar todo, excepto la última de las libertades humanas: la elección de la actitud personal ante un conjunto de circunstancias.',
                        'traducciones' => [
                            2 => 'Everything can be taken from a man but one thing: the last of human freedoms—to choose one’s attitude in any given set of circumstances.',
                            3 => 'Tudo pode ser tirado de um homem, exceto uma coisa: a última das liberdades humanas — escolher sua atitude em qualquer conjunto de circunstâncias.',
                            4 => 'Tutto può essere tolto a un uomo tranne una cosa: l\'ultima delle libertà umane, quella di scegliere il proprio atteggiamento in qualsiasi serie di circostanze.',
                            5 => 'On peut tout enlever à un homme, sauf une chose : la dernière des libertés humaines, celle de choisir son attitude face à une situation donnée.',
                            7 => '人所拥有的一切都可以被剥夺，唯独人性最后的自由——在任何境遇中选择自己态度的自由——无法被夺走。',
                            8 => '人所擁有的一切都可以被剝奪，唯獨人性最後的自由——在任何境遇中選擇自己態度的自由——無法被奪走。',
                        ]
                    ],
                    [
                        'orden' => 2,
                        'tema' => 'El poder de tener un "porqué"',
                        'texto' => 'Quien tiene un porqué para vivir, puede soportar casi cualquier cómo. Descubrir un significado transforma el sufrimiento en fortaleza.',
                        'traducciones' => [
                            2 => 'He who has a why to live can bear almost any how. Discovering meaning transforms suffering into inner strength.',
                            3 => 'Aquele que tem um porquê para viver pode suportar quase qualquer como. Descobrir um significado transforma o sofrimento em fortaleza.',
                            4 => 'Chi ha un perché per vivere puede soportar quase qualquer como.',
                            5 => 'Celui qui a une raison de vivre peut supporter presque n\'importe quel comment.',
                            7 => '拥有生存意义的人，几乎可以承受任何痛苦。发现意义将苦难转化为力量。',
                            8 => '擁有生存意義的人，幾乎可以承受任何痛苦。發現意義將苦難轉化為力量。',
                        ]
                    ]
                ]
            ],
            [
                'titulo' => 'Padre Rico, Padre Pobre',
                'autor' => 'Robert Kiyosaki',
                'descripcion' => 'Lo que los ricos enseñan a sus hijos sobre el dinero que los pobres y la clase media no.',
                'portada_url' => 'https://images.unsplash.com/photo-1553729459-efe14ef6055d?w=600&auto=format&fit=crop&q=80',
                'idioma_original_id' => 2, // Inglés original
                'anio_publicacion' => 1997,
                'tags' => [4, 8], // Finanzas, Educación
                'ensenanzas' => [
                    [
                        'orden' => 1,
                        'tema' => 'Activos vs Pasivos',
                        'texto' => 'The rich acquire assets. The poor and middle class acquire liabilities that they think are assets. An asset puts money in your pocket.',
                        'traducciones' => [
                            1 => 'Los ricos adquieren activos. Los pobres y la clase media adquieren pasivos creyendo que son activos. Un activo pone dinero en tu bolsillo.',
                            3 => 'Os ricos adquirem ativos. Os pobres e a classe média adquirem passivos pensando que são ativos. Um ativo coloca dinheiro no seu bolso.',
                            4 => 'I ricchi acquistano attività. I poveri e la classe media acquistano passività pensando che siano attività. Un\'attività mette soldi in tasca.',
                            5 => 'Les riches acquièrent des actifs. Les pauvres et la classe moyenne acquièrent des passifs en pensant que ce sont des actifs. Un actif met de l\'argent dans votre poche.',
                            7 => '富人购买资产，穷人和中产阶级购买他们自认为是资产的负债。真正的资产会把钱放进你的口袋。',
                            8 => '富人購買資產，窮人和中產階級購買他們自認為是資產的負債。真正的資產會把錢放進你的口袋。',
                        ]
                    ],
                    [
                        'orden' => 2,
                        'tema' => 'No trabajes por dinero, haz que el dinero trabaje para ti',
                        'texto' => 'The poor and middle class work for money. The rich have money work for them. Financial literacy is the ultimate leverage.',
                        'traducciones' => [
                            1 => 'Los pobres y la clase media trabajan por dinero. Los ricos hacen que el dinero trabaje para ellos. La educación financiera es la máxima ventaja.',
                            3 => 'Os pobres e a classe média trabalham pelo dinheiro. Os ricos fazem o dinheiro trabalhar para eles.',
                            4 => 'I poveri e la classe media lavorano per denaro. I ricchi fanno lavorare il denaro per loro.',
                            5 => 'Les pauvres et la classe moyenne travaillent pour l\'argent. Les riches font travailler l\'argent pour eux.',
                            7 => '穷人和中产阶级为金钱工作，富人让金钱为自己工作。财商是最强大的杠杆。',
                            8 => '窮人和中產階級為金錢工作，富人讓金錢為自己工作。財商是最強大的槓桿。',
                        ]
                    ]
                ]
            ]
        ];

        foreach ($booksData as $bData) {
            $libro = Libro::firstOrCreate(
                ['titulo' => $bData['titulo'], 'autor' => $bData['autor']],
                [
                    'descripcion' => $bData['descripcion'],
                    'portada_url' => $bData['portada_url'],
                    'idioma_original_id' => $bData['idioma_original_id'],
                    'anio_publicacion' => $bData['anio_publicacion'],
                    'cantidad_ensenanzas' => count($bData['ensenanzas']),
                    'fecha_procesamiento' => Carbon::now()->subDays(5),
                    'activo' => true,
                ]
            );

            if (isset($bData['tags'])) {
                $libro->tags()->sync($bData['tags']);
            }

            foreach ($bData['ensenanzas'] as $eData) {
                $ensenanza = Ensenanza::firstOrCreate(
                    ['libro_id' => $libro->id, 'orden' => $eData['orden']],
                    [
                        'texto_original' => $eData['texto'],
                        'tema' => $eData['tema'],
                    ]
                );

                if (isset($eData['traducciones'])) {
                    foreach ($eData['traducciones'] as $idiomaId => $textoTraducido) {
                        Traduccion::firstOrCreate(
                            ['ensenanza_id' => $ensenanza->id, 'idioma_id' => $idiomaId],
                            [
                                'texto_traducido' => $textoTraducido,
                                'fecha_traduccion' => Carbon::now()->subDays(3),
                                'veces_usado' => rand(2, 15),
                                'ultimo_uso' => Carbon::now(),
                            ]
                        );
                    }
                }
            }
        }

        // 4. Create Subscriptions for Demo User
        $firstBook = Libro::first();
        if ($firstBook) {
            $sub = Suscripcion::firstOrCreate(
                ['usuario_id' => $clientUsuario->id, 'libro_id' => $firstBook->id],
                [
                    'estado_id' => 1,
                    'ultima_ensenanza_enviada' => 1,
                    'fecha_proximo_envio' => Carbon::now()->addDay(),
                ]
            );

            $sub->idiomas()->sync([1, 2]);
        }
    }
}