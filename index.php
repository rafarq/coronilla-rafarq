<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coronilla de la Divina Misericordia</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#f3f4f6">
    <link rel="apple-touch-icon" href="icons/icon-180x180.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            /* Gray-100 */
            overflow: hidden;
            /* Prevent scrolling */
            overscroll-behavior: none;
            /* Prevent bounce */
            touch-action: none;
            /* Disable browser handling of gestures */
            height: 100vh;
            width: 100vw;
            position: fixed;
            /* Lock body */
            -webkit-user-select: none;
            /* Safari */
            -ms-user-select: none;
            /* IE 10 and IE 11 */
            user-select: none;
            /* Standard syntax */
        }

        .card-container {
            display: grid;
            grid-template-areas: "stack";
            place-items: start center;
            /* Align top, center horizontally */
            width: 100%;
            max-width: 28rem;
            /* max-w-md */
            position: relative;
            perspective: 1000px;
            height: 100%;
            /* Take full available height */
        }

        .card {
            grid-area: stack;
            width: 100%;
            height: auto;
            max-height: calc(100vh - 200px);
            /* Leave space for header */
            overflow-y: auto;
            /* Scroll if content is too long */
            /* Adapt to content */
            background: white;
            border-radius: 24px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            /* Align content to top */
            align-items: center;
            padding: 2rem;
            transition: transform 0.3s ease-out, opacity 0.3s ease-out;
            backface-visibility: hidden;
            will-change: transform, opacity;
            /* Ensure it has a background to cover cards behind */
            z-index: 0;
            /* Custom scrollbar styling */
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        /* Webkit scrollbar */
        .card::-webkit-scrollbar {
            width: 6px;
        }

        .card::-webkit-scrollbar-track {
            background: transparent;
        }

        .card::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 20px;
        }

        /* Stack effect */
        .card:nth-child(1) {
            z-index: 3;
            transform: scale(1) translateY(0);
        }

        .card:nth-child(2) {
            z-index: 2;
            transform: scale(0.95) translateY(10px);
            opacity: 0.4;
            /* Much more transparent */
        }

        .card:nth-child(3) {
            z-index: 1;
            transform: scale(0.9) translateY(20px);
            opacity: 0.2;
            /* Barely visible */
        }

        .card:nth-child(n+4) {
            z-index: 0;
            transform: scale(0.85) translateY(30px);
            opacity: 0;
            display: none;
        }
    </style>
</head>

<body class="flex flex-col justify-start items-center text-gray-800 min-h-screen bg-[#f3f4f6]">

    <div class="sticky top-0 w-full bg-[#f3f4f6] z-50 pt-8 pb-4 px-4 shadow-sm">
        <h1 class="text-xl font-bold text-gray-800 text-center mb-4">Coronilla de la Divina Misericordia</h1>

        <!-- Progress Bar -->
        <div class="w-full max-w-md mx-auto">
            <div class="w-full bg-gray-200 rounded-full h-1.5 relative mt-4">
                <div id="progress-bar" class="bg-blue-600 h-1.5 rounded-full transition-all duration-300 relative z-10"
                    style="width: 0%"></div>
                <div id="progress-markers" class="absolute top-0 left-0 w-full h-full pointer-events-none"></div>
            </div>
        </div>
    </div>

    <div class="card-container px-4 mt-8 flex-grow" id="stack">
        <div class="card" data-index="0" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Señal de la cruz</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">En el nombre del Padre y del Hijo y del Espíritu Santo. Amén.</p></div><div class="card" data-index="1" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Padre nuestro</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Padre nuestro que estás en el cielo,
santificado sea tu Nombre;
venga a nosotros tu Reino;
hágase tu voluntad
en la tierra como en el cielo.
Danos hoy
nuestro pan de cada día;
perdona nuestras ofensas,
como también nosotros perdonamos
a los que nos ofenden;
no nos dejes caer en la tentación,
y líbranos del mal. Amén.</p></div><div class="card" data-index="2" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Ave María</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Dios te salve, María,
llena eres de gracia;
el Señor es contigo.
Bendita Tú eres
entre todas las mujeres,
y bendito es el fruto de tu vientre, Jesús.
Santa María, Madre de Dios,
ruega por nosotros, pecadores,
ahora y en la hora de nuestra muerte. Amén</p></div><div class="card" data-index="3" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Credo</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Creo en Dios, Padre Todopoderoso, Creador del cielo y de la tierra. Creo en Jesucristo, su único Hijo, Nuestro Señor, que fue concebido por obra y gracia del Espíritu Santo, nació de Santa María Virgen, padeció bajo el poder de Poncio Pilato, fue crucificado, muerto y sepultado, descendió a los infiernos, al tercer día resucitó de entre los muertos, subió a los cielos y está sentado a la derecha de Dios, Padre todopoderoso. Desde allí ha de venir a juzgar a vivos y muertos. Creo en el Espíritu Santo, la santa Iglesia católica, la comunión de los santos, el perdón de los pecados, la resurrección de la carne y la vida eterna.
Amén</p></div><div class="card" data-index="4" style="background-color: #EAEAEA;"><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano Mayor 1</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Padre Eterno, Te ofrezco el Cuerpo, la Sangre, el Alma y la Divinidad de Tu amadísimo Hijo, Nuestro Señor Jesucristo, como propiciación de nuestros pecados y los del mundo entero.</p></div><div class="card" data-index="5" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 11</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="6" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 12</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="7" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 13</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="8" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 14</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="9" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 15</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="10" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 16</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="11" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 17</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="12" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 18</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="13" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 19</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="14" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 20</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="15" style="background-color: #EAEAEA;"><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano Mayor 2</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Padre Eterno, Te ofrezco el Cuerpo, la Sangre, el Alma y la Divinidad de Tu amadísimo Hijo, Nuestro Señor Jesucristo, como propiciación de nuestros pecados y los del mundo entero.</p></div><div class="card" data-index="16" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 21</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="17" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 22</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="18" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 23</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="19" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 24</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="20" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 25</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="21" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 26</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="22" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 27</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="23" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 28</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="24" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 29</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="25" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 30</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="26" style="background-color: #EAEAEA;"><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano Mayor 3</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Padre Eterno, Te ofrezco el Cuerpo, la Sangre, el Alma y la Divinidad de Tu amadísimo Hijo, Nuestro Señor Jesucristo, como propiciación de nuestros pecados y los del mundo entero.</p></div><div class="card" data-index="27" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 31</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="28" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 32</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="29" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 33</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="30" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 34</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="31" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 35</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="32" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 36</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="33" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 37</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="34" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 38</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="35" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 39</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="36" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 40</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="37" style="background-color: #EAEAEA;"><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano Mayor 4</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Padre Eterno, Te ofrezco el Cuerpo, la Sangre, el Alma y la Divinidad de Tu amadísimo Hijo, Nuestro Señor Jesucristo, como propiciación de nuestros pecados y los del mundo entero.</p></div><div class="card" data-index="38" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 41</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="39" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 42</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="40" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 43</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="41" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 44</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="42" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 45</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="43" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 46</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="44" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 47</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="45" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 48</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="46" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 49</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="47" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 50</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="48" style="background-color: #EAEAEA;"><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano Mayor 5</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Padre Eterno, Te ofrezco el Cuerpo, la Sangre, el Alma y la Divinidad de Tu amadísimo Hijo, Nuestro Señor Jesucristo, como propiciación de nuestros pecados y los del mundo entero.</p></div><div class="card" data-index="49" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 51</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="50" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 52</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="51" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 53</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="52" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 54</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="53" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 55</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="54" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 56</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="55" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 57</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="56" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 58</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="57" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 59</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="58" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Grano menor 60</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Por Su dolorosa Pasión,
ten misericordia de nosotros
y del mundo entero.</p></div><div class="card" data-index="59" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Invocación 1</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Santo Dios, Santo Fuerte, Santo Inmortal, ten misericordia de nosotros y del mundo entero.</p></div><div class="card" data-index="60" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Invocación 2</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Santo Dios, Santo Fuerte, Santo Inmortal, ten misericordia de nosotros y del mundo entero.</p></div><div class="card" data-index="61" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Invocación 3</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Santo Dios, Santo Fuerte, Santo Inmortal, ten misericordia de nosotros y del mundo entero.</p></div><div class="card" data-index="62" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">Oración Conclusión</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">Oh Dios Eterno, en quien la misericordia es infinita y el tesoro de compasión inagotable, vuelve a nosotros Tu mirada bondadosa y aumenta Tu misericordia en nosotros, para que en momentos difíciles no nos desesperemos ni nos desalentemos, sino que, con gran confianza, nos sometamos a Tu santa voluntad, que es el Amor y la Misericordia mismos. Amén.</p></div><div class="card" data-index="63" style=""><h2 class="text-2xl font-semibold mb-6 text-center text-gray-900">FINAL</h2><p class="text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line">En el nombre del Padre y del Hijo y del Espíritu Santo. Amén.</p></div>    </div>

    <div class="mt-8 text-gray-400 text-sm animate-pulse">
        Desliza a la derecha &rarr;
    </div>

    <script src="script.js?v=2"></script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('sw.js')
                .then(() => console.log('Service Worker Registered'))
                .catch((error) => console.log('Service Worker Registration Failed:', error));
        }
    </script>
</body>

</html>