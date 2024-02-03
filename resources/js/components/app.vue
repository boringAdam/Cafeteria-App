<template>
  <div>
    <h1>Cafeteria App</h1>
    <h3> Remaining Budget: {{ remainingBalance }} HUF</h3><br>

    <div v-if="warningMessage" class="warning-message">
      <p v-html="warningMessage"></p>
    </div>
    
    <expense-form @submit="submitExpense"></expense-form>

    <div>
      <br>
      <h2>Amount of spending per category</h2>
      <table v-if="categories.length > 0">
        <thead>
          <tr>
            <th>Category</th>
            <th>Amount Spent</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="category in categories" :key="category.category">
            <td>{{ category.category }}</td>
            <td>{{ parseInt(category.sum) }} HUF</td>
          </tr>
        </tbody>
      </table>
      <p v-else>No spending recorded yet.</p>
    </div>

    <div>
      <h3>All Spendings</h3>
      <table v-if="allSpendings.length > 0">
        <thead>
          <tr>
            <th>Amount Spent</th>
            <th>Month</th>
            <th>Category</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="spending in allSpendings" :key="spending.id">
            <td>{{ spending.amount_spent }} HUF</td>
            <td>{{ spending.month }}</td>
            <td>{{ spending.category }}</td>
          </tr>
        </tbody>
      </table>  
      <p v-else>No spending recorded yet.</p>
    </div>

    <div>
      <button @click="downloadCsv">Download CSV</button>
    </div>
  </div>
</template>


<script>

  import { ref, onMounted } from 'vue';
  import '../../css/app.css';

  export default {
    data() {
      return {  
        remainingBalance: 0,
        categories: [],
        allSpendings: [],
        amount_spent: 0,
        month: '',
        category: '',
        warningMessage: '',
      };
    },
    methods: {
      async fetchBalance() {
        try {
          const response = await fetch('/api/get-existing-balance');
          const data = await response.json();
          this.remainingBalance = data.remaining_balance;
        } catch (error) {
          console.error('Error fetching existing balance', error);
        }
      },

      async fetchCategories() {
        try {
          const response = await fetch('/api/get-category-spending');
          const data = await response.json();
          this.categories = data;
        } catch (error) {
          console.error('Error fetching category spending', error);
        }
      },

      async fetchAllSpendings() {
        try {
          const response = await fetch('/api/get-all-spendings');
          const data = await response.json();
          this.allSpendings = data;
        } catch (error) {
          console.error('Error fetching all spendings', error);
        }
      },

      async downloadCsv() {
        try {
          const response = await fetch('/download-csv');
          const blob = await response.blob();

          const url = window.URL.createObjectURL(new Blob([blob]));
          const link = document.createElement('a');
          link.href = url;
          link.setAttribute('download', 'cafeteria_spendings.csv');
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        } catch (error) {
          console.error('Error downloading CSV', error);
        }
      },

      async submitExpense(expenseData) {
        try {
          const token = document.head.querySelector('meta[name="csrf-token"]').content;

          await fetch('/submit-expense', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify(expenseData),
          });

          this.warningMessage = '';
          this.amount_spent = 0;
          this.month = '';
          this.category = '';

          await Promise.all([ this.fetchBalance(), 
                              this.fetchCategories(), 
                              this.fetchAllSpendings()]);

        } catch (error) {
          if (error.response) {
            if (error.response && error.response.status === 422 && error.response.data.error) {
              this.warningMessage = error.response.data.error;
            } else {
              console.error('Error submitting expense. Server responded with:', error.response.status, error.response.data);
            }
          } else if (error.request) {
            console.error('Error submitting expense. No response received from the server.');
          } else {
            console.error('Error submitting expense. Request setup error:', error.message);
          }
        }
      },
    },
    mounted() {
      this.fetchBalance();
      this.fetchCategories();
      this.fetchAllSpendings();
    },
  };

</script>
