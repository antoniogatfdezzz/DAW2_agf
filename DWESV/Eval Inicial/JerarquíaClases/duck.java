class Duck extends Animal {
    private String beakColor = "yellow";

    public Duck(int age, String gender) {
        super(age, gender);
    }

    public void swim() {
        System.out.println("Pato nada.");
    }

    public void quack() {
        System.out.println("Pato hace quack.");
    }

    public String getBeakColor() {
        return beakColor;
    }
}