const { createApp } = Vue;

// Configure axios defaults
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

createApp({
  data() {
    return {
      formData: {
        name: "",
        teamCount: 2,
      },
      currentTournament: null,
      message: "",
      error: "",
    };
  },
  computed: {
    gamesByRound() {
      if (!this.currentTournament) return {};
      return this.currentTournament.games.reduce((acc, game) => {
        if (!acc[game.round]) acc[game.round] = [];
        acc[game.round].push(game);
        return acc;
      }, {});
    },
    sortedTeams() {
      if (!this.currentTournament) return [];
      return [...this.currentTournament.teams].sort((a, b) => {
        if (b.wins !== a.wins) return b.wins - a.wins;
        return b.winRate - a.winRate;
      });
    },
  },
  methods: {
    async createTournament() {
      try {
        const response = await axios.post("/tournament/create", this.formData);
        if (response.data.tournamentId) {
          // Update URL and browser history
          const newUrl = `/tournament/${response.data.tournamentId}`;
          window.history.pushState({}, "", newUrl);

          // Load tournament results
          await this.loadTournament(response.data.tournamentId);
        }
        this.message = "Tournament created successfully!";
        this.error = null;
      } catch (error) {
        this.error =
          error.response?.data?.error || "Failed to create tournament";
        this.message = null;
      }
    },
    async loadTournament(id) {
      try {
        const response = await axios.get(`/tournament/${id}`);
        this.currentTournament = response.data;
        this.message = null;
        this.error = null;
      } catch (error) {
        this.error = error.response?.data?.error || "Failed to load tournament";
        this.message = null;
      }
    },
    getPositionClass(team) {
      const index = this.sortedTeams.indexOf(team);
      if (index === 0) return "gold";
      if (index === 1) return "silver";
      if (index === 2) return "bronze";
      return "";
    },
    resetTournament() {
      this.currentTournament = null;
      this.formData.name = "";
      this.message = "";
      this.error = "";
    },
  },
  async mounted() {
    // Load tournament data if an ID was provided on page load
    if (window.INITIAL_TOURNAMENT_ID) {
      await this.loadTournament(window.INITIAL_TOURNAMENT_ID);
    }
  },
}).mount("#app");
