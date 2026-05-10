const searchInput = document.getElementById("searchInput");
const dietFilter = document.getElementById("dietFilter");

function filterMeals() {

  const searchValue = searchInput.value.toLowerCase();

  const dietValue = dietFilter.value;

  const filteredMeals = meals.filter(meal => {

    const matchesSearch =
      meal.name.toLowerCase().includes(searchValue);

    const matchesDiet =
      dietValue === "" ||
      meal.tags.includes(dietValue);

    return matchesSearch && matchesDiet;

  });

  displayMeals(filteredMeals);
}

searchInput.addEventListener("keyup", filterMeals);

dietFilter.addEventListener("change", filterMeals);