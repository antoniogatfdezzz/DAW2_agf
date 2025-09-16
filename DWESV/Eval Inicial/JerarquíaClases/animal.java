
public class Animal {
    protected int age;
    protected String gender;

    public Animal(int age, String gender) {
        this.age = age;
        this.gender = gender;
    }

    public boolean isMammal() {
        return false;
    }

    public void mate() {
        System.out.println("El animal se está reproduciendo...");
    }
}