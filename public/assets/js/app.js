const { createApp } = Vue

createApp({
    data() {
        return {
            formData: {
                name: '',
                teamCount: 2
            },
            currentTournament: null,
            message: '',
            error: ''
        }
    },
    computed: {
        gamesByRound() {
            if (!this.currentTournament) return {}
            return this.currentTournament.games.reduce((acc, game) => {
                if (!acc[game.round]) acc[game.round] = []
                acc[game.round].push(game)
                return acc
            }, {})
        },
        sortedTeams() {
            if (!this.currentTournament) return []
            return [...this.currentTournament.teams].sort((a, b) => {
                if (b.wins !== a.wins) return b.wins - a.wins
                return b.winRate - a.winRate
            })
        }
    },
    methods: {
        async createTournament() {
            try {
                const response = await axios.post('/tournament/create', this.formData)
                if (response.data.tournamentId) {
                    this.loadTournament(response.data.tournamentId)
                }
            } catch (error) {
                this.error = error.response?.data?.error || 'Failed to create tournament'
            }
        },
        async loadTournament(id) {
            try {
                const response = await axios.get(`/tournament/results?id=${id}`)
                this.currentTournament = response.data
                this.error = ''
            } catch (error) {
                this.error = 'Failed to load tournament'
            }
        },
        getPositionClass(team) {
            const index = this.sortedTeams.indexOf(team)
            if (index === 0) return 'gold'
            if (index === 1) return 'silver'
            if (index === 2) return 'bronze'
            return ''
        },
        resetTournament() {
            this.currentTournament = null
            this.formData.name = ''
            this.message = ''
            this.error = ''
        }
    }
}).mount('#app') 