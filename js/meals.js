const meals = [

  {
    id: 1,
    name: "Grilled Chicken Bowl",
    calories: 450,
    image: "images/meal1.jpg",
    tags: ["high-protein"],
    ingredients: [
      "Chicken",
      "Rice",
      "Broccoli"
    ]
  },

  {
    id: 2,
    name: "Vegan Avocado Salad",
    calories: 320,
    image: "images/meal2.jpg",
    tags: ["vegan"],
    ingredients: [
      "Avocado",
      "Tomatoes",
      "Lettuce"
    ]
  }

];

const mealContainer = document.getElementById("mealContainer");

function displayMeals(mealList) {

  mealContainer.innerHTML = "";

  mealList.forEach(meal => {

    mealContainer.innerHTML += `

      <div class="col-md-4 mb-4">

        <div class="card meal-card shadow-sm">

          <img src="${meal.image}" class="card-img-top">

          <div class="card-body">

            <h5 class="card-title">
              ${meal.name}
            </h5>

            <p>
              🔥 ${meal.calories} Calories
            </p>

            <div class="mb-2">
              ${meal.tags.map(tag =>
                `<span class="badge bg-success">${tag}</span>`
              ).join(" ")}
            </div>

            <p>
              ${meal.ingredients.join(", ")}
            </p>

            <button class="btn btn-success w-100">
              View Details
            </button>

          </div>

        </div>

      </div>

    `;
  });
}

displayMeals(meals);