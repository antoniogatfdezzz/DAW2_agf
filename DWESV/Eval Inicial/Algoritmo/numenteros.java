import java.util.*;
import java.util.stream.Collectors;

public class numenteros {
    public static List<Integer> ordenarNumeros(List<Integer> numeros) {
        List<Integer> parMay100 = numeros.stream()
            .filter(n -> n > 100 && n % 2 == 0)
            .sorted(Comparator.reverseOrder())
            .collect(Collectors.toList());

        List<Integer> mult3 = numeros.stream()
            .filter(n -> n % 3 == 0 && !(n > 100 && n % 2 == 0))
            .sorted()
            .collect(Collectors.toList());

        List<Integer> resto = numeros.stream()
            .filter(n -> !(n > 100 && n % 2 == 0) && n % 3 != 0)
            .collect(Collectors.toList());

        List<Integer> resultado = new ArrayList<>();
        resultado.addAll(parMay100);
        resultado.addAll(mult3);
        resultado.addAll(resto);

        return resultado;
    }

    public static void main(String[] args) {
        Scanner teclado = new Scanner(System.in);
        System.out.print("Introduce los numeros separados por comas (,): ");
        String entrada = teclado.nextLine();
        List<Integer> lista = Arrays.stream(entrada.split(","))
            .map(String::trim)
            .filter(s -> !s.isEmpty())
            .map(Integer::parseInt)
            .collect(Collectors.toList());
        List<Integer> ordenados = ordenarNumeros(lista);
        System.out.println(ordenados);
    }
}