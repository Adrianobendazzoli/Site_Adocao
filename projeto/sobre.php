<?php
/*
AUTO-COMMENTED FILE
Original path: site-adocao1/projeto/sobre.php
Summary (auto-generated):
styling (CSS) present

Notes:
- This header was generated automatically to give a quick overview of the file.
- Inline, line-by-line commenting was NOT applied automatically to avoid changing behavior.
- If you want detailed line-by-line comments for specific files, ask and I'll produce them.
*/
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-image: url("img-site/fundo.PNG");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
            color: rgb(255, 255, 255);
        }


        .titulo {
            font-family: 'Poppins', sans-serif;
            font-weight: 900;
            font-size: 40px;
            text-transform: uppercase;
            color: #fffefe;

        }

        a {
            margin-right: 30px;
            outline: none;
            color: white;
            text-decoration: none;
        }


        .sobre {
            text-align: center;
            padding: 3rem 1rem;
        }

        .titulo {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 2rem;
        }


        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            max-width: 900px;
            margin: 0 auto 3rem;
        }

        .card {
            background-color: #7a45b3;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: left;
        }

        .card-titulo {
            background-color: #4a148c;
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 12px;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        /* ==== INTEGRANTES ==== */
        .integrantes {
            background-color: #7a45b3;
            max-width: 1000px;
            margin: 0 auto;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .integrantes-titulo {
            background-color: #4a148c;
            display: inline-block;
            padding: 0.6rem 1.2rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .integrantes-lista {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 2rem;
            justify-items: center;
        }

        /* Estilo de cada membro */
        .membro {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
            width: 160px;
            padding: 1rem;
            color: #4a148c;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .membro:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }

        /* Foto do membro */
        .membro img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 0.8rem;
        }

        /* Nome e função */
        .membro h4 {
            font-size: 1rem;
            font-weight: 700;
            margin: 0.3rem 0;
        }

        .membro p {
            font-size: 0.9rem;
            font-weight: 500;
            color: #6a1b9a;
            margin: 0;
        }

    
    </style>
</head>

<body>

    <section class="sobre">
        <h2 class="titulo">SOBRE NOSSO PROJETO</h2>

        <div class="cards">
            <div class="card">
                <h3 class="card-titulo">QUEM SOMOS?</h3>
                <p>
                    Somos um projeto de adoção de qualquer tipo de pet!
                    Com sistema de localização para calcular sua distância até o seu futuro pet.
                </p>
            </div>

            <div class="card">
                <h3 class="card-titulo">O QUE FAZEMOS?</h3>
                <p>
                    Compartilhamos pets que estão precisando de donos, além de que qualquer usuário cadastrado
                    consegue achar um dono para um pet carente também!
                </p>
            </div>
        </div>


        <div class="integrantes">
            <h3 class="integrantes-titulo">INTEGRANTES</h3>

            <div class="integrantes-lista">

                <div class="membro">
                    <img src="img-site/dri.jpg" alt="Foto do integrante 1">
                    <h4>Adriano Bendazzoli</h4>

                </div>


                <div class="membro">
                    <img src="img-site/alysson.jpg" alt="Foto do integrante 2">
                    <h4>Alysson Rocha</h4>

                </div>


                <div class="membro">
                    <img src="img-site/daniel.jpg" alt="Foto do integrante 3">
                    <h4>Daniel Diogo</h4>

                </div>


                <div class="membro">
                    <img src="img-site/felipe.jpg" alt="Foto do integrante 4">
                    <h4>Felipe José</h4>

                </div>


                <div class="membro">
                    <img src="img-site/moretti.jpg" alt="Foto do integrante 5">
                    <h4>Guilherme Moretti</h4>

                </div>
            </div>
        </div>
    </section>
    <div class="w-full text-center mt-6">
        <a href="home.php"
            class="px-6 py-2 min-w-[120px] text-center bg-white text-violet-600 border border-violet-600 rounded hover:bg-violet-600 hover:text-white active:bg-indigo-500 focus:outline-none focus:ring inline-block">
            Voltar para a tela inicial
        </a>
    </div>

</body>

</html>