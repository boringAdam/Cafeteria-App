<template>
  <div>
    <button @click="showForm">Add new spending</button>

    <h3 v-if="showExpenseForm">Add New Expense</h3>
    
    <form v-if="showExpenseForm" @submit.prevent="submitExpense" method="post">
      <label for="amount_spent">Amount Spent:</label>
      <input type="number" v-model="amount_spent" required>

      <label for="month">Month:</label>
      <select v-model="month" required>
        <option value="January">January</option>
        <option value="February">February</option>
        <option value="March">March</option>
        <option value="April">April</option>
        <option value="May">May</option>
        <option value="June">June</option>
        <option value="July">July</option>
        <option value="August">August</option>
        <option value="September">September</option>
        <option value="October">October</option>
        <option value="November">November</option>
        <option value="December">December</option>
      </select>

      <label for="category">Category:</label>
      <select v-model="category" required>
        <option value="Food">Food</option>
        <option value="Drink">Drink</option>
        <option value="Sweet">Sweet</option>
      </select>

      <button type="submit">Submit Expense</button>
    </form>
  </div>
</template>

<script>
  export default {
    data() {
      return {
        showExpenseForm: false,
        amount_spent: 0,
        month: '',
        category: '',
      };
    },
    methods: {
      showForm() {
        this.showExpenseForm = true;
      },
      submitExpense() {
        const expenseData = {
          amount_spent: this.amount_spent,
          month: this.month,
          category: this.category,
        };

        this.$emit('submit', expenseData);
        this.amount_spent = 0;
        this.month = '';
        this.category = '';
        this.showExpenseForm = false;
      },
    },
  };
</script>
