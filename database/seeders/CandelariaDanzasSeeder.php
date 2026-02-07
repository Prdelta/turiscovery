<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class CandelariaDanzasSeeder extends Seeder
{
    /**
     * Seed the candelaria_danzas table with traditional dances
     * Contenido agregado por el administrador
     */
    public function run(): void
    {
        // Obtener el admin que agregará el contenido
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->command->error('⚠️  No se encontró usuario admin. Ejecuta DatabaseSeeder primero.');
            return;
        }

        $danzas = [
            // DANZAS MESTIZAS
            [
                'name' => 'Diablada Puneña',
                'type' => 'mestiza',
                'description' => 'La Diablada es la danza emblemática de la Festividad de la Virgen de la Candelaria. Representa la lucha entre el bien y el mal, donde San Miguel Arcángel vence a Lucifer y sus diablos.',
                'history' => 'Surgió en el siglo XVIII en las minas de Puno como una expresión de resistencia cultural. Los mineros indígenas fusionaron elementos católicos con creencias andinas ancestrales. La máscara del diablo representa al Supay (deidad andina del inframundo) cristianizado. En 1576 ya se tienen registros de esta danza en las festividades coloniales.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e5/Diablada_de_Puno.jpg/1200px-Diablada_de_Puno.jpg',
                'video_url' => 'https://www.youtube.com/watch?v=example1',
                'region' => 'Puno, Altiplano',
                'characteristics' => 'Trajes elaborados con pedrería, lentejuelas y bordados en oro. Máscaras de diablo con dragones, serpientes y animales míticos. Coreografía con saltos acrobáticos y giros. Música con bandas de bronces y bombos. Personajes: Diablo Mayor, China Supay, Ángel Miguel, 7 pecados capitales.',
                'order' => 1,
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Morenada',
                'type' => 'mestiza',
                'description' => 'Danza que rinde homenaje a los esclavos africanos traídos durante la colonia para trabajar en las minas de Potosí. Sus movimientos pesados simbolizan el esfuerzo de los trabajadores bajo el yugo colonial.',
                'history' => 'Tiene sus orígenes en el siglo XVI cuando esclavos negros fueron traídos al Alto Perú. La danza mezcla elementos africanos y andinos. El nombre "Morenada" deriva de "moreno" en referencia a los trabajadores de piel oscura. Representa la dura labor minera y el sincretismo cultural entre tres continentes: América, África y Europa.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f8/Morenada_Puno_Peru.jpg/1200px-Morenada_Puno_Peru.jpg',
                'video_url' => 'https://www.youtube.com/watch?v=example2',
                'region' => 'Puno, Lago Titicaca',
                'characteristics' => 'Trajes pesados de más de 20 kg con matracas metálicas. Máscaras de moreno con rasgos africanos. Movimiento característico de balanceo lento. Personajes: Rey Moreno, Cholita, Achachi, Kusillo. Música con tarkas, bombos y platillos. Coreografía en filas con pasos sincronizados.',
                'order' => 2,
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Llamerada',
                'type' => 'mestiza',
                'description' => 'Danza que representa a los arrieros que transportaban mercancías en caravanas de llamas entre Puno, Arequipa y Bolivia. Celebra la importancia de la llama en la economía y cultura andina.',
                'history' => 'Surgió en el altiplano puneño como tributo a los llameros que recorrían grandes distancias comercializando lana, charqui y otros productos. La llama fue el principal medio de transporte de carga en los Andes antes de la llegada española. La danza evolucionó durante el siglo XIX incorporando elementos festivos a la labor cotidiana de los pastores.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d9/Llamerada_Puno.jpg/1200px-Llamerada_Puno.jpg',
                'video_url' => 'https://www.youtube.com/watch?v=example3',
                'region' => 'Puno, Altiplano',
                'characteristics' => 'Movimientos que imitan el caminar de la llama. Trajes con mantas aguayo, chullos y ponchos de colores. Uso de watanas (lazos) y Ch\'uspas (bolsas tejidas). Coreografía con saltos y zapateos. Personajes: Llamero, Palla, Llama. Música con zampoñas, quenas y bombo.',
                'order' => 3,
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Waca Waca (Waca Tokhoris)',
                'type' => 'mestiza',
                'description' => 'Danza satírica que parodia las corridas de toros traídas por los españoles. Representa la resistencia cultural indígena frente a las imposiciones coloniales.',
                'history' => 'Creada durante la colonia como una forma de burla hacia las corridas de toros españolas, evento exclusivo de la élite colonial. Los indígenas la utilizaron para expresar su rechazo a costumbres ajenas. "Waca" significa vaca/toro en quechua. La danza muestra cómo el toro (símbolo de poder español) es vencido por el torero indígena, invirtiendo las jerarquías coloniales.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b2/Waca_Waca_Candelaria.jpg/1200px-Waca_Waca_Candelaria.jpg',
                'video_url' => 'https://www.youtube.com/watch?v=example4',
                'region' => 'Puno, Azángaro',
                'characteristics' => 'Trajes de toreros con chaquetillas bordadas y capas. Máscaras caricaturescas de españoles con narices grandes. Personajes: Torero, Toro (danzarín con armazón), Señorita, Kusillo. Movimientos humorísticos y acrobáticos. Música festiva con bandas y zampoñas.',
                'order' => 4,
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Sicuris o Sikuris',
                'type' => 'mestiza',
                'description' => 'Conjunto musical y danzario que ejecuta la siku (zampoña) mientras danza. Representa la tradición musical milenaria del altiplano y la comunión con la naturaleza.',
                'history' => 'Tiene raíces preincaicas. Los sikus (zampoñas) se tocan desde hace más de 2000 años en el altiplano. Durante el Tawantinsuyo, los sikuris participaban en ceremonias religiosas. La tradición se mantuvo durante la colonia adaptándose a contextos festivos católicos. Cada comunidad desarrolló su propio estilo de ejecución.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/33/Sikuris_Puno_Festival.jpg/1200px-Sikuris_Puno_Festival.jpg',
                'video_url' => 'https://www.youtube.com/watch?v=example5',
                'region' => 'Puno, Juli, Huancané',
                'characteristics' => 'Músicos que tocan zampoñas (sikus) de diferentes tamaños: ch\'uli, malta, sanka. Movimientos circulares y saltos coordinados. Trajes con chalecos bordados, sombreros y ponchos coloridos. Sistema musical de contrapunto: ira y arka. Danza comunal que requiere sincronización perfecta.',
                'order' => 5,
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Kullawada',
                'type' => 'mestiza',
                'description' => 'Danza que representa a las hilanderas y tejedoras aymaras. Celebra el arte textil andino y el rol fundamental de la mujer en la economía familiar del altiplano.',
                'history' => 'Nació en las comunidades aymaras del altiplano boliviano-peruano durante el periodo colonial. El tejido era (y es) una actividad económica vital para las familias andinas. "Kullawa" significa hermana en aymara. La danza muestra todo el proceso del tejido: esquila, hilado, teñido y tejido en telar. Fue incorporada a la Candelaria en el siglo XX.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/k/k5/Kullawada_Puno.jpg/1200px-Kullawada_Puno.jpg',
                'video_url' => 'https://www.youtube.com/watch?v=example6',
                'region' => 'Puno, Zona Aymara',
                'characteristics' => 'Mujeres con ruecas (pushkas) y ovillos de lana. Trajes de bayeta, polleras de colores brillantes, mantas aguayo. Hombres con ponchos y sombreros. Movimientos que simulan el hilado. Personajes: Kullawa (hilandera), Lechero, Kusillo. Música con tarkas, bombos y triángulos.',
                'order' => 6,
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Caporales',
                'type' => 'mestiza',
                'description' => 'Danza que representa a los capataces de las haciendas coloniales. Se caracteriza por sus movimientos acrobáticos y sus trajes llamativos con cascabeles.',
                'history' => 'Surgió en Bolivia en 1969 derivada de la Saya Afroboliviana, creada por los hermanos Estrada. El "Caporal" era el capataz que vigilaba a los esclavos en haciendas y minas. Llegó a Puno en la década de 1970 y se popularizó rápidamente entre los jóvenes. Es una de las danzas más jóvenes pero de mayor difusión internacional.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/27/Caporales_Puno.jpg/1200px-Caporales_Puno.jpg',
                'video_url' => 'https://www.youtube.com/watch?v=example7',
                'region' => 'Puno, origen boliviano',
                'characteristics' => 'Trajes brillantes con lentejuelas y botas con cascabeles. Movimientos acrobáticos: saltos, giros, splits. Látigo (chicote) que el caporal hace tronar. Personajes: Caporal, Cholita. Música con ritmo de saya y tinkus. Coreografía enérgica y juvenil. Requiere excelente condición física.',
                'order' => 7,
                'is_featured' => false,
                'is_active' => true,
            ],

            // DANZAS AUTÓCTONAS
            [
                'name' => 'Qhashwa de Ichu',
                'type' => 'autoctona',
                'description' => 'Danza amorosa de cortejo donde jóvenes quechuas expresan sus sentimientos a través de movimientos suaves y galanteos. Representa el enamoramiento en las comunidades altoandinas.',
                'history' => 'Es una de las danzas más antiguas del altiplano, de origen preincaico. "Qhashwa" significa danza en quechua. Se ejecutaba durante festividades agrícolas y religiosas ancestrales. Sobrevivió a la conquista manteniéndose pura en comunidades alejadas. Es un ritual de cortejo donde los jóvenes eligen pareja para la futura cosecha.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9c/Qhashwa_Puno.jpg/1200px-Qhashwa_Puno.jpg',
                'video_url' => 'https://www.youtube.com/watch?v=example8',
                'region' => 'Puno, Melgar, Azángaro',
                'characteristics' => 'Vestimenta tradicional de comunidad: chullos, chumpis, polleras y llicllas. Movimientos circulares y zapateo suave. Uso de flores y pañuelos en el cortejo. Música con quenas, pinkillos y tinya. Danza en parejas con formaciones circulares. Letra en quechua con temas amorosos.',
                'order' => 8,
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Tinku',
                'type' => 'autoctona',
                'description' => 'Danza guerrera ritual que representa el encuentro o combate entre comunidades. Es una ofrenda a la Pachamama donde la sangre derramada fertiliza la tierra.',
                'history' => 'Proviene de comunidades aymaras de Bolivia y Puno. "Tinku" significa encuentro o pelea ritual en aymara. Era (y es) una práctica ancestral donde comunidades se enfrentan ritualmente durante fiestas patronales. La sangre derramada es ofrenda a la Pachamama para una buena cosecha. Se practica desde tiempos preincaicos como forma de resolver conflictos y renovar lazos comunales.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ab/Tinku_Puno_Candelaria.jpg/1200px-Tinku_Puno_Candelaria.jpg',
                'video_url' => 'https://www.youtube.com/watch?v=example9',
                'region' => 'Puno, frontera con Bolivia',
                'characteristics' => 'Trajes de bayeta con chumpis anchos de cuero. Movimientos de combate y defensa, saltos altos. Cascos decorados (monteras) y protectores en manos. Hombres con postura agresiva, puños cerrados. Música con pututus, wankaras y erkes. Zapateo fuerte que retumba la tierra. Representa fuerza y valentía.',
                'order' => 9,
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Ayarachi',
                'type' => 'autoctona',
                'description' => 'Danza fúnebre ceremonial ejecutada con zampoñas gigantes. Los Ayarachis eran los músicos sagrados del imperio Inca que acompañaban procesiones religiosas y funerales.',
                'history' => 'Es la danza más antigua de Puno, con más de 500 años. "Ayarachi" proviene del aymara "aya" (espíritu) y "rachi" (lamento). Eran músicos especializados del Tawantinsuyo que tocaban en ceremonias fúnebres del Inca y la nobleza. Cada comunidad tenía sus ayarachis hereditarios. La tradición se mantiene en Paratia, Lampa y Ayaviri desde tiempos preincaicos.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/78/Ayarachi_Puno.jpg/1200px-Ayarachi_Puno.jpg',
                'video_url' => 'https://www.youtube.com/watch?v=example10',
                'region' => 'Paratia (Lampa), Ayaviri',
                'characteristics' => 'Vestimenta ceremonial con ponchos negros y rojos. Zampoñas gigantes (sikus grandes) llamadas sankas. Movimientos lentos y solemnes en círculo. Música pentatónica melancólica y profunda. Solo hombres pueden ser ayarachis (tradición hereditaria). Danza de profundo contenido espiritual y místico.',
                'order' => 10,
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Puli Puli',
                'type' => 'autoctona',
                'description' => 'Danza carnavalesca de las comunidades aymaras que representa la alegría de la época de lluvias y la siembra. Se caracteriza por su ritmo festivo y colorido.',
                'history' => 'Danza de origen aymara vinculada al calendario agrícola andino. "Puli Puli" es una onomatopeya del viento que sopla en febrero durante carnavales. Se ejecuta durante el anata (carnaval andino) cuando las primeras lluvias fertilizan los campos sembrados. Es una celebración de la renovación de la vida y la abundancia que traerá la cosecha.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5a/Puli_Puli_Candelaria.jpg/1200px-Puli_Puli_Candelaria.jpg',
                'video_url' => 'https://www.youtube.com/watch?v=example11',
                'region' => 'Puno, zona aymara',
                'characteristics' => 'Trajes multicolores con serpentinas y flores. Movimientos alegres y saltos. Uso de wiphalas y guirnaldas. Música con tarkas y bombos. Danza mixta en parejas y grupos. Cantos en aymara sobre la chacra y la cosecha. Ritmo festivo y contagioso. Serpentinas que representan las lluvias.',
                'order' => 11,
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Wititis',
                'type' => 'autoctona',
                'description' => 'Danza de cortejo del valle del Colca, Arequipa, pero también ejecutada en Puno. Representa el enamoramiento entre jóvenes campesinos durante las faenas agrícolas.',
                'history' => 'Aunque originaria del Colca (Arequipa), se practica en zonas altas de Puno. Fue declarada Patrimonio Cultural Inmaterial de la Humanidad por UNESCO en 2015. Representa el ciclo de cortejo campesino: desde la timidez inicial hasta el compromiso. Los movimientos imitan aves endémicas del valle. Su práctica se remonta a tiempos preincaicos.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/15/Wititis_Danza.jpg/1200px-Wititis_Danza.jpg',
                'video_url' => 'https://www.youtube.com/watch?v=example12',
                'region' => 'Puno (adoptada), Arequipa',
                'characteristics' => 'Vestidos blancos bordados con lentejuelas multicolores. Sombreros decorados con cintas y flores. Movimientos de cortejo: persecución, esquive, abrazo. Zapateo rítmico sincronizado. Música con quenas, acordeón y triángulo. Coreografía que narra una historia de amor. Danza muy alegre y romántica.',
                'order' => 12,
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Kallahuaya',
                'type' => 'autoctona',
                'description' => 'Danza que representa a los médicos andinos tradicionales (kallawayas) que viajaban por el Tawantinsuyo curando con plantas medicinales.',
                'history' => 'Los Kallawayas fueron médicos itinerantes del imperio Inca, famosos por su conocimiento de plantas medicinales. Provenían de la región de Charazani (Bolivia) y recorrían todo el Tawantinsuyo. Hablaban un idioma secreto (Machaj Juyay) para proteger sus conocimientos. Fueron los médicos personales del Inca. Su sabiduría médica aún se practica en comunidades andinas.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c8/Kallahuaya_Danza.jpg/1200px-Kallahuaya_Danza.jpg',
                'video_url' => 'https://www.youtube.com/watch?v=example13',
                'region' => 'Puno, frontera Bolivia',
                'characteristics' => 'Trajes con chuspas llenas de hierbas medicinales. Sombreros con plumas y elementos rituales. Movimientos que simulan la recolección de plantas. Uso de bastones ceremoniales. Personajes: Kallawaya (curandero), Ayudantes. Música ceremonial con ritmo lento. Danza con significado ritual y medicinal.',
                'order' => 13,
                'is_featured' => false,
                'is_active' => true,
            ],
        ];

        foreach ($danzas as $danza) {
            DB::table('candelaria_danzas')->insert([
                'user_id' => $admin->id, // Agregado por el admin
                'name' => $danza['name'],
                'type' => $danza['type'],
                'description' => $danza['description'],
                'history' => $danza['history'],
                'image_url' => $danza['image_url'],
                'video_url' => $danza['video_url'],
                'region' => $danza['region'],
                'characteristics' => $danza['characteristics'],
                'order' => $danza['order'],
                'is_featured' => $danza['is_featured'],
                'is_active' => $danza['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $mestizas = count(array_filter($danzas, fn($d) => $d['type'] === 'mestiza'));
        $autoctonas = count(array_filter($danzas, fn($d) => $d['type'] === 'autoctona'));

        $this->command->info('✅ Danzas creadas exitosamente:');
        $this->command->info("   → {$mestizas} danzas mestizas");
        $this->command->info("   → {$autoctonas} danzas autóctonas");
        $this->command->info("   → Total: " . count($danzas) . " danzas");
        $this->command->info("   → Agregadas por: {$admin->name} (Admin)");
    }
}
