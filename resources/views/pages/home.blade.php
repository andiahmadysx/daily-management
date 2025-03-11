@extends('layouts.app')


@push('styles')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.8.0/chart.min.js"></script>
@endpush


@section('content')
    <div id="dashboard-view">
        <div class="header">
           <div class="ham-wrapper">
               <div class="hamburger-menu" id="hamburger-menu">
                   <div class="hamburger-icon">
                       <span></span>
                       <span></span>
                       <span></span>
                   </div>
               </div>

               <h1 class="section-title">
                   Dashboard
               </h1>
           </div>
            <div class="date" id="current-date">March 6, 2025</div>
        </div>

        <div class="quote" id="quote-container">

        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Weekly Productivity</h2>
            </div>
            <div class="chart-container">
                <canvas id="productivity-chart"></canvas>
            </div>
        </div>

        <div class="recent-activity">
            <div class="card recent-tasks">
                <div class="card-header">
                    <h2 class="card-title">Recent Tasks</h2>
                </div>
                <div id="recent-tasks-container">
                    <!-- Recent tasks will be loaded here -->

                    @foreach($tasks as $task)
                        <div class="activity-item">
                            <div class="activity-dot activity-task"
                                 style="background-color: {{$task->priority_color}}"></div>
                            <div class="activity-content">
                                <div class="activity-title">{{$task->title}}</div>
                                <div class="activity-meta">{{$task->is_completed ? 'Completed' : 'Active'}} •
                                    {{$task->priority}} priority
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="card recent-journals">
                <div class="card-header">
                    <h2 class="card-title">Recent Journal Entries</h2>
                </div>
                <div id="recent-journals-container">
                    @foreach($journals as $journal)
                        <div class="activity-item">
                            <div class="activity-dot activity-journal"></div>
                            <div class="activity-content">
                                <div class="activity-title">{{$journal->title}}</div>
                                <div class="activity-meta">{{$journal->created_at}}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>

        // generate rumi quotes
        const rumiQuotes = [
            "What you seek is seeking you.",
            "The wound is the place where the Light enters you.",
            "Stop acting so small. You are the universe in ecstatic motion.",
            "Yesterday I was clever, so I wanted to change the world. Today I am wise, so I am changing myself.",
            "Raise your words, not voice. It is rain that grows flowers, not thunder.",
            "You were born with wings, why prefer to crawl through life?",
            "Let yourself be silently drawn by the strange pull of what you really love. It will not lead you astray.",
            "The minute I heard my first love story, I started looking for you, not knowing how blind that was. Lovers don't finally meet somewhere. They're in each other all along.",
            "Don't grieve. Anything you lose comes round in another form.",
            "Forget safety. Live where you fear to live. Destroy your reputation. Be notorious.",
            "Don't be satisfied with stories, how things have gone with others. Unfold your own myth.",
            "Everything in the universe is within you. Ask all from yourself.",
            "Silence is the language of God, all else is poor translation.",
            "Let the beauty of what you love be what you do.",
            "Wherever you are, and whatever you do, be in love.",
            "Your task is not to seek for love, but merely to seek and find all the barriers within yourself that you have built against it.",
            "Dance, when you're broken open. Dance, if you've torn the bandage off. Dance in the middle of the fighting. Dance in your blood. Dance when you're perfectly free.",
            "The garden of the world has no limits, except in your mind.",
            "Why should I stay at the bottom of a well when a strong rope is in my hand?",
            "Be like the sun for grace and mercy. Be like the night to cover others' faults. Be like running water for generosity. Be like death for rage and anger. Be like the Earth for modesty. Appear as you are. Be as you appear.",
            "In your light I learn how to love. In your beauty, how to make poems. You dance inside my chest where no one sees you, but sometimes I do, and that sight becomes this art.",
            "This being human is a guest house. Every morning a new arrival. A joy, a depression, a meanness, some momentary awareness comes as an unexpected visitor. Welcome and entertain them all!",
            "Out beyond ideas of wrongdoing and rightdoing, there is a field. I'll meet you there.",
            "The art of knowing is knowing what to ignore.",
            "When you do things from your soul, you feel a river moving in you, a joy.",
            "If you are irritated by every rub, how will your mirror be polished?",
            "Only from the heart can you touch the sky.",
            "Set your life on fire. Seek those who fan your flames.",
            "Sell your cleverness and buy bewilderment.",
            "Don't you know yet? It is your light that lights the worlds.",
            "The only lasting beauty is the beauty of the heart.",
            "Words are a pretext. It is the inner bond that draws one person to another, not words.",
            "Let yourself become living poetry.",
            "There is a candle in your heart, ready to be kindled. There is a void in your soul, ready to be filled. You feel it, don't you?",
            "I want to sing like the birds sing, not worrying about who hears or what they think.",
            "Silence is an ocean. Speech is a river.",
            "When you feel a peaceful joy, that's when you are near truth.",
            "There are a thousand ways to kneel and kiss the ground; there are a thousand ways to go home again.",
            "Be empty of worrying. Think of who created thought.",
            "Close your eyes. Fall in love. Stay there.",
            "There is a voice that doesn't use words. Listen.",
            "When the soul lies down in that grass, the world is too full to talk about.",
            "The lion is most handsome when looking for food.",
            "Let go of your mind and then be mindful. Close your ears and listen!",
            "We are stars wrapped in skin. The light you are seeking has always been within.",
            "I belong to no religion. My religion is love. Every heart is my temple.",
            "Why do you stay in prison when the door is so wide open?",
            "Your heart knows the way. Run in that direction.",
            "Start a huge, foolish project, like Noah. It makes absolutely no difference what people think of you.",
            "Be grateful for whoever comes, because each has been sent as a guide from beyond.",
            "The Prophets accept all agony and trust it. For the water has never feared the fire.",
            "Half-heartedness doesn't reach into majesty.",
            "Love is the bridge between you and everything.",
            "If light is in your heart, you will find your way home.",
            "What is planted in each person's soul will sprout.",
            "May these vows and this marriage be blessed.",
            "In the blackest of your moments, wait with no fear.",
            "Be melting snow. Wash yourself of yourself.",
            "With every breath, I plant the seeds of devotion, I am a farmer of the heart.",
            "Live life as if everything is rigged in your favor.",
            "The universe is not outside of you. Look inside yourself; everything that you want, you already are.",
            "Love risks everything and asks for nothing.",
            "The moon stays bright when it doesn't avoid the night.",
            "Either give me more wine or leave me alone.",
            "When I am with you, we stay up all night. When you're not here, I can't go to sleep.",
            "Run from what's comfortable. Forget safety. Live where you fear to live.",
            "My soul is from elsewhere, I'm sure of that, and I intend to end up there.",
            "The very center of your heart is where life begins - the most beautiful place on earth.",
            "Respond to every call that excites your spirit.",
            "Seek the wisdom that will untie your knot. Seek the path that demands your whole being.",
            "Don't wait any longer. Dive in the ocean, leave and let the sea be you.",
            "Goodbyes are only for those who love with their eyes. Because for those who love with heart and soul, there is no such thing as separation.",
            "Travel brings power and love back into your life.",
            "Reason is powerless in the expression of Love.",
            "I have been a seeker and I still am, but I stopped asking the books and the stars. I started listening to the teaching of my Soul.",
            "If all you can do is crawl, start crawling.",
            "All your anxiety is because of your desire for harmony. Seek disharmony, then you will gain peace.",
            "Be full of sorrow, that you may become hill of joy; tears may produce pearls.",
            "Come out of the circle of time and into the circle of love.",
            "Move outside the tangle of fear-thinking. Live in silence.",
            "I should be suspicious of what I want.",
            "Your depression is connected to your insolence and refusal to praise.",
            "A mountain keeps an echo deep inside itself. That's how I hold your voice.",
            "Sit, be still, and listen.",
            "You have to keep breaking your heart until it opens.",
            "The moment you accept what troubles you've been given, the door will open.",
            "Wear gratitude like a cloak and it will feed every corner of your life.",
            "There is a life-force within your soul, seek that life. There is a gem in the mountain of your body, seek that mine.",
            "This place is a dream. Only a sleeper considers it real.",
            "Before death takes away what you are given, give away what there is to give.",
            "However much I might try to expound or explain Love, when I come to Love itself, I am ashamed of my explanations.",
            "Whoever finds love beneath hurt and grief disappears into emptiness with a thousand new disguises.",
            "The breeze at dawn has secrets to tell you. Don't go back to sleep.",
            "When you lose all sense of self, the bonds of a thousand chains will vanish.",
            "Sorrow prepares you for joy. It violently sweeps everything out of your house, so that new joy can find space to enter.",
            "The morning wind spreads its fresh smell. We must get up and take that in, that wind that lets us live. Breathe before it's gone.",
            "I have neither a soul nor a body, for I come from the very Soul of all souls.",
            "Dancing is not just getting up painlessly, like a leaf blown on the wind; dancing is when you tear your heart out and rise out of your body to hang suspended between the worlds.",
            "If in thirst you drink water from a cup, you see God in it. Those who are not in love with God will see only their own faces in it.",
            "All people on the planet are children, except for a very few. No one is grown up except those free of desire.",
            "Be a lamp, or a lifeboat, or a ladder. Help someone's soul heal. Walk out of your house like a shepherd.",
            "Conventional opinion is the ruin of our souls.",
            "Gratitude is the wine for the soul. Go on. Get drunk.",
            "Knock, and He'll open the door. Vanish, and He'll make you shine like the sun. Fall, and He'll raise you to the heavens. Become nothing, and He'll turn you into everything.",
            "The desire to know your own soul will end all other desires.",
            "Ignore those that make you fearful and sad, that degrade you back towards disease and death.",
            "Joy lives concealed in grief.",
            "Beauty surrounds us, but usually we need to be walking in a garden to know it.",
            "You left and I cried tears of blood. My sorrow grows. It's not just that You left. But when You left my eyes went with You. Now, how will I cry?",
            "Where there is ruin, there is hope for a treasure.",
            "Everyone has been made for some particular work, and the desire for that work has been put in every heart.",
            "Very little grows on jagged rock. Be ground. Be crumbled, so wildflowers will come up where you are.",
            "I know you're tired but come, this is the way.",
            "You think because you understand 'one' you must also understand 'two', because one and one make two. But you must also understand 'and'.",
            "That which is false troubles the heart, but truth brings joyous tranquility.",
            "Let the waters settle and you will see the moon and the stars mirrored in your own being.",
            "I lost my hat while gazing at the moon, and then I lost my mind.",
            "Lovers don't finally meet somewhere. They're in each other all along.",
            "Something opens our wings. Something makes boredom and hurt disappear. Someone fills the cup in front of us: We taste only sacredness.",
            "Study me as much as you like, you will not know me, for I differ in a hundred ways from what you see me to be. Put yourself behind my eyes and see me as I see myself, for I have chosen to dwell in a place you cannot see.",
            "Listen with ears of tolerance! See through the eyes of compassion! Speak with the language of love.",
            "Let silence take you to the core of life.",
            "As you start to walk on the way, the way appears.",
            "You are not a drop in the ocean. You are the entire ocean in a drop.",
            "Patience is the key to joy.",
            "Let go of your worries and be completely clear-hearted, like the face of a mirror that contains no images.",
            "Inside you there's an artist you don't know about.",
            "What hurts you, blesses you. Darkness is your candle.",
            "Seek the sound that never ceases. Seek the sun that never sets.",
            "Let silence be the art you practice.",
            "The quieter you become, the more you are able to hear.",
            "Be with those who help your being.",
            "If you want to be more alive, love is the truest health.",
            "Caught by our own thoughts, we worry about everything.",
            "I am not this hair, I am not this skin, I am the soul that lives within.",
            "The soul has been given its own ears to hear things the mind does not understand.",
            "This is love: to fly toward a secret sky, to cause a hundred veils to fall each moment. First to let go of life. Finally, to take a step without feet.",
            "When the world pushes you to your knees, you're in the perfect position to pray.",
            "On a day when the wind is perfect, the sail just needs to open and the world is full of beauty.",
            "But listen to me. For one moment quit being sad. Hear blessings dropping their blossoms around you.",
            "Stay with friends who support you in these. Talk with them about sacred texts, and how you are doing, and how they are doing, and keep your practices together.",
            "A thousand half-loves must be forsaken to take one whole heart home.",
            "There's a field somewhere beyond all doubt and wrong doing. I'll meet you there.",
            "Dance until you shatter yourself.",
            "I am so close, I may look distant. So completely mixed with you, I may look separate. So out in the open, I appear hidden. So silent, because I am constantly talking with you.",
            "I searched for God and found only myself. I searched for myself and found only God.",
            "Become the sky. Take an axe to the prison wall. Escape.",
            "Shine like the whole universe is yours.",
            "Lovers find secret places inside this violent world where they make transactions with beauty.",
            "There is a basket of fresh bread on your head, yet you go door to door asking for crusts.",
            "Each moment contains a hundred messages from God.",
            "We come spinning out of nothingness, scattering stars like dust.",
            "The source of now is here.",
            "I once had a thousand desires. But in my one desire to know you, all else melted away.",
            "There is a way between voice and presence where information flows. In disciplined silence it opens.",
            "If you find me not within you, you will never find me. For I have been with you, from the beginning of me.",
            "All I have seen teaches me to trust the Creator for all I have not seen.",
            "Thankfulness brings you to the place where the beloved lives.",
            "Whatever lifts the corners of your mouth, trust that.",
            "Be like a tree and let the dead leaves drop.",
            "In each moment the fire rages, it will burn away a hundred veils. And carry you a thousand steps toward your goal.",
            "Let yourself be drawn by the stronger pull of that which you truly love.",
            "Look at the moon in the sky, not the one in the lake.",
            "You've seen my descent, now watch my rising.",
            "Let your teacher be love itself.",
            "Let the lover be disgraceful, crazy, absentminded. Someone sober will worry about things going badly. Let the lover be.",
            "If the foot of the trees were not tied to earth, they would be pursuing me. For I have blossomed so much, I am the envy of the gardens.",
            "Seek the path that demands your whole being.",
            "Poetry can be dangerous, especially beautiful poetry, because it gives the illusion of having had the experience without actually going through it.",
            "This is how I would die into the love I have for you: As pieces of cloud dissolve in sunlight.",
            "The message behind the words is the voice of the heart.",
            "You have seen your own strength. You have seen your own beauty. You have seen your golden wings. Why do you worry?",
            "Discard yourself and thereby regain yourself. Spread the trap of humility and ensnare love.",
            "I am yours. Don't give myself back to me.",
            "Always remember you are braver than you believe, stronger than you seem, smarter than you think and twice as beautiful as you've ever imagined.",
            "In the house of lovers, the music never stops, the walls are made of songs and the floor dances.",
            "Not only the thirsty seek the water, the water as well seeks the thirsty.",
            "We carry inside us the wonders we seek outside us.",
            "Give up to grace. The ocean takes care of each wave 'til it gets to shore.",
            "Gamble everything for love, if you're a true human being.",
            "The beauty you see in me is a reflection of you.",
            "By God, when you see your beauty you will be the idol of yourself.",
            "This is a subtle truth. Whatever you love, you are.",
            "Love is an emerald. Its brilliant light wards off dragons on this treacherous path.",
            "There is no salvation for the soul but to fall in love. Only lovers can escape out of these two worlds.",
            "Love is the cure, for your pain will keep giving birth to more pain until your eyes constantly exhale love as effortlessly as your body yields its scent.",
            "Peaceful is the one who's not concerned with having more or less.",
            "Each has to enter the nest made by the other imperfect bird.",
            "In every religion, there is love, yet love has no religion.",
            "Why are you so enchanted by this world, when a mine of gold lies within you?",
            "A wealth you cannot imagine flows through you. Do not consider what strangers say. Be secluded in your secret heart-house, that bowl of silence.",
            "Come, seek, for search is the foundation of fortune: every success depends on focusing the heart.",
            "The only thing matters is love, all pain is just unturned love.",
            "Look past your thoughts, so you may drink the pure nectar of This Moment.",
            "The spirit is so near that you can't see it! But reach for it... don't be a jar, full of water, whose rim is always dry. Don't be the rider who gallops all night and never sees the horse that is beneath him.",
            "Beg of God the removal of envy, that God may deliver you from externals, and bestow upon you an inward occupation, which will absorb you so that your attention is not drawn away.",
            "You have within you more love than you could ever understand.",
            "Life is a balance between holding on and letting go.",
            "All your anxiety is because of your desire for harmony. Seek disharmony, then you will gain peace.",
            "Would you become a pilgrim on the road of love? The first condition is that you make yourself humble as dust and ashes.",
            "Be drunk with love, for love is all that exists.",
            "In each moment the fire rages, it will burn away a hundred veils. And carry you a thousand steps toward your goal.",
            "When the ocean sends you a wave, ride it.",
            "Lovers find secret places inside this violent world where they make transactions with beauty.",
            "Do not feel lonely, the entire universe is inside you.",
            "Be a lamp, or a lifeboat, or a ladder. Help someone's soul heal. Walk out of your house like a shepherd.",
            "The whole universe is contained within a single human being – you.",
            "Don't turn away. Keep your eyes on the bandaged place. That's where the light enters.",
            "Achieve some perfection yourself, so that you may not fall into sorrow by seeing the perfection in others.",
            "The time has come to turn your heart into a temple of fire. Your essence is gold hidden in dust. To reveal its splendor, you need to burn in the fire of love.",
            "Be an empty page, untouched by words.",
            "The happiest people don't have the best of everything but they make the best of everything they have.",
            "I want to sing like birds sing, not worrying who listens or what they think.",
            "Whatever purifies you is the right path, I will not try to define it. Let go of your worries and be completely clear-hearted, like the face of a mirror that contains no images. If you want a clear mirror, behold yourself and see the shameless truth, which the mirror reflects.",
            "The lamps are different, but the light is the same.",
            "When you feel a peaceful joy, that's when you are near truth.",
            "In silence there is eloquence. Stop weaving and see how the pattern improves.",
            "What matters is how quickly you do what your soul directs.",
            "There are hundreds of ways to kneel and kiss the ground.",
            "Keep silent, because the world of silence is a vast fullness.",
            "Water in the boat is the ruin of the boat, but water under the boat is the support of the boat.",
            "Hearts are like tapers, which at beauteous eyes, Kindle a flame of love that never dies; And beauty is a flame, where hearts, like moths, Offer themselves a burning sacrifice.",
            "The ground's generosity takes in our compost and grows beauty! Try to be more like the ground.",
            "Remember. The way you make love is the way God will be with you.",
            "Observe the wonders as they occur around you. Don't claim them. Feel the artistry moving through and be silent.",
            "Wisdom tells us we are not worthy; love tells us we are. My life flows between the two.",
            "I searched for God among the Christians and on the Cross and therein I found Him not. I went into the ancient temples of idolatry; no trace of Him was there. I entered the mountain cave of Hira and then went as far as Qandhar but God I found not. With set purpose I fared to the summit of Mount Caucasus and found there only 'anqa's habitation. Then I directed my search to the Kaaba, the resort of old and young; God was not there even. Turning to philosophy I inquired about him from ibn Sina but found Him not within his range. I fared then to the scene of the Prophet's experience of a great divine manifestation only a 'two bow-lengths' distance from him' but God was not there even in that exalted court. Finally, I looked into my own heart and there I saw Him; He was nowhere else.",
            "Love will find its way through all languages on its own.",
            "That which God said to the rose, and caused it to laugh in full-blown beauty, He said to my heart, and made it a hundred times more beautiful.",
            "Make peace with the universe. Take joy in it. It will turn to gold. Resurrection will be now. Every moment, a new beauty.",
            "Don't try to steer the boat. Don't open shop for yourself. Listen. Keep silent. You are not God's mouthpiece. Try to be an ear, and if you do speak, ask for explanations.",
            "The ground's generosity takes in our compost and grows beauty. Try to be more like the ground.",
            "Love is not an emotion, it's your very existence.",
            "The whole universe is contained within a single human being – you.",
            "Love said to me, there is nothing that is not me. Be silent.",
            "Love is the water of life. Everything other than love for the most beautiful God is agony of the spirit, though it be sugar-eating. What is agony of the spirit? To advance toward death without seizing hold of the water of life.",
            "When someone is counting out gold for you, don't look at your hands, or the gold. Look at the giver.",
            "Everything that is made beautiful and fair and lovely is made for the eye of one who sees."
        ];
        const generateQuote = () => {
            document.querySelector('.quote').textContent = rumiQuotes[Math.floor(Math.random() * rumiQuotes.length)] + ' - Rumi';
        }

        generateQuote();

        const productivityChartCtx = document.getElementById('productivity-chart').getContext('2d');

        function getCurrentWeekDates() {
            const now = new Date();
            const dayOfWeek = now.getDay();
            const diff = dayOfWeek === 0 ? 6 : dayOfWeek - 1;

            const monday = new Date(now);
            monday.setDate(now.getDate() - diff);

            const weekDates = [];
            const dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

            for (let i = 0; i < 7; i++) {
                const date = new Date(monday);
                date.setDate(monday.getDate() + i);
                weekDates.push({
                    date: date,
                    formatted: `${dayNames[i]} ${date.getDate()}`,
                    dayName: dayNames[i]
                });
            }

            return weekDates;
        }


        function fetchWeeklyProductivityData() {
            const weekDates = getCurrentWeekDates();


            const startDate = formatDateForAPI(weekDates[0].date);
            const endDate = formatDateForAPI(weekDates[6].date);

            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '{{ route("dashboard.weeklyProductivity") }}',
                    type: 'GET',
                    data: {
                        start_date: startDate,
                        end_date: endDate
                    },
                    success: function (response) {
                        resolve({
                            weekDates: weekDates,
                            productivityData: processProductivityData(response, weekDates)
                        });
                    },
                    error: function (error) {
                        console.error("Error fetching productivity data:", error);


                        resolve({
                            weekDates: weekDates,
                            productivityData: generateMockProductivityData(weekDates)
                        });
                    }
                });
            });
        }


        function formatDateForAPI(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }


        function processProductivityData(response, weekDates) {

            const completedTasks = Array(7).fill(0);
            const totalTasks = Array(7).fill(0);


            if (response && response.data) {
                response.data.forEach(dayData => {

                    const date = new Date(dayData.date);
                    const dayIndex = weekDates.findIndex(d =>
                        d.date.getDate() === date.getDate() &&
                        d.date.getMonth() === date.getMonth()
                    );

                    if (dayIndex !== -1) {
                        completedTasks[dayIndex] = dayData.completed_tasks || 0;
                        totalTasks[dayIndex] = dayData.total_tasks || 0;
                    }
                });
            }

            return {
                completedTasks,
                totalTasks
            };
        }


        function generateMockProductivityData(weekDates) {
            const completedTasks = [];
            const totalTasks = [];


            const today = new Date();
            const currentDayIndex = weekDates.findIndex(d =>
                d.date.getDate() === today.getDate() &&
                d.date.getMonth() === today.getMonth()
            );

            for (let i = 0; i < 7; i++) {

                if (i <= currentDayIndex) {
                    totalTasks.push(Math.floor(Math.random() * 8) + 3);
                    completedTasks.push(Math.floor(Math.random() * (totalTasks[i] - 1)) + 1);
                } else {

                    totalTasks.push(Math.floor(Math.random() * 5));
                    completedTasks.push(Math.floor(Math.random() * totalTasks[i]));
                }
            }

            return {
                completedTasks,
                totalTasks
            };
        }


        function calculateProductivityPercentage(completed, total) {
            if (total === 0) return 0;
            return (completed / total) * 100;
        }


        function initProductivityChart() {
            fetchWeeklyProductivityData().then(({weekDates, productivityData}) => {

                const productivityPercentages = productivityData.completedTasks.map((completed, i) => {
                    return calculateProductivityPercentage(completed, productivityData.totalTasks[i]);
                });


                const labels = weekDates.map(d => d.dayName);


                const datasets = [
                    {
                        label: 'Tasks Completed',
                        data: productivityData.completedTasks,
                        backgroundColor: 'rgba(65, 131, 215, 0.2)',
                        borderColor: 'rgba(65, 131, 215, 1)',
                        borderWidth: 2,
                        order: 2,
                        type: 'bar'
                    },
                    {
                        label: 'Total Tasks',
                        data: productivityData.totalTasks,
                        backgroundColor: 'rgba(171, 183, 197, 0.2)',
                        borderColor: 'rgba(171, 183, 197, 1)',
                        borderWidth: 2,
                        order: 1,
                        type: 'bar'
                    },
                    {
                        label: 'Productivity %',
                        data: productivityPercentages,
                        backgroundColor: 'rgba(88, 214, 141, 0)',
                        borderColor: 'rgba(88, 214, 141, 1)',
                        borderWidth: 3,
                        type: 'line',
                        order: 0,
                        yAxisID: 'percentage',
                        tension: 0.4,
                        pointBackgroundColor: 'rgba(88, 214, 141, 1)',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ];


                const productivityChart = new Chart(productivityChartCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Number of Tasks'
                                },
                                grid: {
                                    display: true,
                                    drawBorder: true,
                                    color: 'rgba(200, 200, 200, 0.2)'
                                }
                            },
                            percentage: {
                                beginAtZero: true,
                                max: 100,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Completion %'
                                },
                                grid: {
                                    display: false
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const datasetLabel = context.dataset.label;
                                        const value = context.parsed.y;

                                        if (datasetLabel === 'Productivity %') {
                                            return `${datasetLabel}: ${value.toFixed(1)}%`;
                                        }

                                        const index = context.dataIndex;
                                        const completed = productivityData.completedTasks[index];
                                        const total = productivityData.totalTasks[index];

                                        if (datasetLabel === 'Tasks Completed') {
                                            return `${datasetLabel}: ${completed} of ${total}`;
                                        } else {
                                            return `${datasetLabel}: ${value}`;
                                        }
                                    }
                                }
                            }
                        }
                    }
                });

                return productivityChart;
            }).catch(error => {
                console.error("Failed to initialize productivity chart:", error);
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Chart !== 'undefined') {
                initProductivityChart();
            } else {
                const script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js';
                script.integrity = 'sha512-ElRFoEQdI5Ht6kZvyzXhcG0NTJz0N8BzkC/wAA4C6/hfClJuM4ZJsi2Wnp0GpvvtbWNsVCsv+gYTgCw5VIfnQ==';
                script.crossOrigin = 'anonymous';
                script.referrerPolicy = 'no-referrer';
                script.onload = initProductivityChart;
                document.head.appendChild(script);
            }


        });
    </script>
@endpush
