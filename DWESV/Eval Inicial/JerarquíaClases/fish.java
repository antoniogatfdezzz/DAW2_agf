class Fish extends Animal {
    private int sizeInFt;
    private boolean canEat;

    public Fish(int age, String gender, int sizeInFt, boolean canEat) {
        super(age, gender);
        this.sizeInFt = sizeInFt;
        this.canEat = canEat;
    }

    private void swim() {
        System.out.println("Pez nada.");
    }

    
    public void startSwimming() {
        swim();
    }
}