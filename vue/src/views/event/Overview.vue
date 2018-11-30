<template>
    <div>
        <div :key="event.id" v-for="event in events">
            <EventCard :event="event"/>
        </div>
    </div>
</template>

<script>
    import EventCard from '@/components/EventCard';

    export default {
        name: 'Overview',
        components: {EventCard},
        data: function () {
            return {
                events: []
            };
        },
        methods: {
            initSample: function () {
                this.events = [

                    {
                        'id': '1',
                        'title': 'ToArray',
                        'description': 'An array is a data structure that contains a group of elements. Typically these elements are all of the same data type, such as an integer or string. Arrays are commonly used in computer programs to organize data so that a related set of values can be easily sorted or searched.',
                        'location': 'Aula Erlimatt',
                        'color': '#4286f4',
                        'from': '2018-04-10 19:00:00',
                        'to': '2018-04-10 21:00:00'
                    },
                    {
                        'id': '2',
                        'title': 'Coding For Dummies',
                        'description': 'Coding For Dummies is the perfect location to start! Explore the basics of HTML, CSS, JavaScript, Ruby, and Python and find out how to build great web applications. Once youre up and running, you can continue to refine and practice your skills online',
                        'location': 'Aula Erlimatt',
                        'color': '#4286f4',
                        'from': '2018-04-10 19:00:00',
                        'to': '2018-04-10 21:00:00'
                    },
                    {
                        'id': '3',
                        'title': 'Harry porter and the sorcerers stone',
                        'description': 'For 10 years, Harry lives in the cupboard under the stairs and is subjected to cruel mistreatment by Aunt Petunia, Uncle Vernon and their son Dudley. On his 11th birthday, Harry receives a letter inviting him to study magic at the Hogwarts School of Witchcraft and Wizardry.',
                        'location': 'Aula Erlimatt',
                        'color': '#000000',
                        'from': '2018-04-10 19:00:00',
                        'to': '2018-04-10 21:00:00'
                    },
                    {
                        'id': '4',
                        'title': 'Harry porter and the sorcerers stone 2',
                        'description': 'For 10 years, Harry lives in the cupboard under the stairs and is subjected to cruel mistreatment by Aunt Petunia, Uncle Vernon and their son Dudley. On his 11th birthday, Harry receives a letter inviting him to study magic at the Hogwarts School of Witchcraft and Wizardry.',
                        'location': 'Aula Erlimatt',
                        'color': '#ffffff',
                        'from': '2018-04-10 19:00:00',
                        'to': '2018-04-11 21:00:00'
                    }
                ];
            }
        },
        created() {
            this.initSample();
        }
    };
</script>

<style scoped>

</style>
